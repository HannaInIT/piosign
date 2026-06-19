<?php

namespace Tests\Feature;

use App\Enums\SyncStatus;
use App\Jobs\SyncSignatureJob;
use App\Models\Employee;
use App\Services\GoogleGmailService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Throwable;

class SyncSignatureJobTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_syncs_signature_and_marks_employees_as_synced(): void
    {
        $employee = Employee::factory()->create();

        $this->mock(GoogleGmailService::class)
            ->shouldReceive('updateUserSignature')
            ->once()
            ->with($employee->email, Mockery::type('string'));

        (new SyncSignatureJob($employee))->handle(app(GoogleGmailService::class));

        $this->assertDataBaseHas('employees', [
            'signature_sync_status' => SyncStatus::Synced,
        ]);

        $this->assertNotNull($employee->fresh()->signature_last_synced_at);
    }

    #[Test]
    public function it_marks_employee_as_failed_when_exception_is_thrown(): void
    {
        $employee = Employee::factory()->create();

        $this->mock(GoogleGmailService::class)
            ->shouldReceive('updateUserSignature')
            ->andThrow(new Exception('Google API error'));

        $job = new SyncSignatureJob($employee);

        try {
            $job->handle(app(GoogleGmailService::class));

        } catch (Throwable $exception) {
            $job->failed($exception);
        }

        $this->assertDataBaseHas('employees', [
            'signature_sync_status' => SyncStatus::Failed,
        ]);
    }
}
