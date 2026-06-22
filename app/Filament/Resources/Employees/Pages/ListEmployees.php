<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Resources\Employees\EmployeeResource;
// use Filament\Actions\CreateAction;
use App\Jobs\SyncGoogleWorkspaceUsersJob;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListEmployees extends ListRecords
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('syncGoogleWorkspace')
                ->label('Sync from Google Workspace')
                ->icon('heroicon-o-arrow-path')
                ->action(function () {
                    dispatch_sync(new SyncGoogleWorkspaceUsersJob);
                }),
        ];
    }
}
