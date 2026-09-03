<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Resources\Employees\EmployeeResource;
use App\Jobs\SyncSignatureJob;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditEmployee extends EditRecord
{
    protected static string $resource = EmployeeResource::class;

    public function getHeading(): string
    {
        return 'Edit employee';
    }

    public function getBreadcrumbs(): array
    {
        return [
            EmployeeResource::getUrl() => 'Employees',
            $this->record->full_name => $this->record->full_name,
            'Edit',
        ];
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('syncSignature')
                ->label('Sync signature to Gmail')
                ->icon('heroicon-o-rocket-launch')
                ->action(function () {
                    dispatch_sync(new SyncSignatureJob($this->record));
                }),
        ];
    }
}
