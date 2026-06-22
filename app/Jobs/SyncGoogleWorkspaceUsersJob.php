<?php

namespace App\Jobs;

use App\Models\Employee;
use App\Services\GoogleDirectoryService;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncGoogleWorkspaceUsersJob implements ShouldQueue
{
    use Queueable;

    public function handle(GoogleDirectoryService $googleDirectoryService): void
    {
        $users = $googleDirectoryService->listUsers();

        $allGoogleIds = $users->pluck('google_id');

        $upsertData = $users->map(fn (array $user) => [
            'google_id' => $user['google_id'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'email' => $user['email'],
            'deleted_at' => null,
        ])->toArray();
        if (! empty($upsertData)) {
            Employee::withTrashed()->upsert($upsertData, ['google_id'], ['first_name', 'last_name', 'email', 'deleted_at']);
        }

        Employee::whereNull('deleted_at')->whereNotIn('google_id', $allGoogleIds)->delete();

        Log::info("Synced {$users->count()} employees from Google Workspace.");

        Notification::make()
            ->title('Sync successful')
            ->body("{$users->count()} users synced from Google Workspace.")
            ->success()
            ->send();
    }

    public function failed(Throwable $exception): void
    {

        report($exception);

        Notification::make()
            ->title('Sync failed')
            ->body('Google Workspace users could not be synced.')
            ->danger()
            ->send();
    }
}
