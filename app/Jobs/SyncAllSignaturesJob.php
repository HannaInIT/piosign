<?php

namespace App\Jobs;

use App\Models\Employee;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncAllSignaturesJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        Employee::whereNull('deleted_at')->each(function (Employee $employee) {
            SyncSignatureJob::dispatch($employee);
        });
    }

    public function failed(Throwable $exception): void
    {
        Log::error('SyncAllSignaturesJob failed: '.$exception->getMessage());
        report($exception);
    }
}
