<?php

namespace Tests\Feature;

use App\Jobs\SyncAllSignaturesJob;
use App\Jobs\SyncSignatureJob;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SyncAllSignaturesJobTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_dispatches_sync_signature_job_for_each_active_employee(): void
    {
        Bus::fake();

        Employee::factory()->count(3)->create();
        $deletedEmployee = Employee::factory()->create();
        $deletedEmployee->delete();

        (new SyncAllSignaturesJob)->handle();

        Bus::assertDispatched(SyncSignatureJob::class, 3);
    }

    #[Test]
    public function it_does_not_dispatch_jobs_for_soft_deleted_employees(): void
    {
        Bus::fake();

        $deletedEmployee = Employee::factory()->create();
        $deletedEmployee->delete();

        (new SyncAllSignaturesJob)->handle();

        Bus::assertNotDispatched(SyncSignatureJob::class);
    }
}
