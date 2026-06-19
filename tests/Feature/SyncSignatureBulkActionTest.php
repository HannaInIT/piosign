<?php

namespace Tests\Feature;

use App\Jobs\SyncSignatureJob;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SyncSignatureBulkActionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_dispatches_sync_signature_job_for_each_selected_employee(): void
    {
        Queue::fake();

        $employees = Employee::factory()->count(3)->create();

        foreach ($employees as $employee) {
            SyncSignatureJob::dispatch($employee);
        }

        Queue::assertCount(3);

        Queue::assertPushed(SyncSignatureJob::class, function ($job) use ($employees) {
            return $employees->contains(fn ($exception) => $exception->id === $job->employee->id);
        });
    }
}
