<?php

namespace App\Jobs;

use App\Models\Employee;
use App\Services\GoogleDirectoryService;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class SyncGoogleWorkspaceUsersJob implements ShouldQueue
{
    use Queueable;

    public function handle(GoogleDirectoryService $googleDirectoryService): void
    {
        $users = $googleDirectoryService->listUsers();

        $googleIds = [];
        foreach ($users as $user) {
            $employee = Employee::withTrashed()
                ->firstOrNew(['google_id' => $user['google_id']]);

            if ($employee->trashed()) {
                $employee->restore();
            }

            $employee->fill([
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'email' => $user['email'],
            ])->save();

            $googleIds[] = $employee->google_id;

        }
        Employee::whereNotIn('google_id', $googleIds)->delete();

        Notification::make()
            ->title('Sync successful')
            ->body("{$users->count()} users synced from Google Workspace.")
            ->success()
            ->send();
    }

    public function failed(Throwable $exception): void
    {
        Notification::make()
            ->title('Sync failed')
            ->body('Google Workspace users could not be synced.')
            ->danger()
            ->send();
    }
}
