<?php

namespace App\Jobs;

use App\Enums\SyncStatus;
use App\Models\Employee;
use App\Services\GoogleGmailService;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class SyncSignatureJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public Employee $employee) {}

    public function handle(GoogleGmailService $googleGmailService): void
    {
        $signatureHtml = view('signature', ['employee' => $this->employee])->render();
        $googleGmailService->updateUserSignature($this->employee->email, $signatureHtml);

        $this->employee->update([
            'signature_sync_status' => SyncStatus::Synced,
            'signature_last_synced_at' => now(),
        ]);

        Notification::make()
            ->title('Sync successful')
            ->body("{$this->employee->full_name}'s signature synced to Gmail.")
            ->success()
            ->send();
    }

    public function failed(Throwable $exception): void
    {
        report($exception);

        $this->employee->update([
            'signature_sync_status' => SyncStatus::Failed,
        ]);

        Notification::make()
            ->title('Sync failed')
            ->body('Signature could not be synced')
            ->danger()
            ->send();
    }
}
