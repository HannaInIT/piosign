<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Resources\Employees\EmployeeResource;
use Filament\Resources\Pages\EditRecord;

class EditEmployee extends EditRecord
{
    protected static string $resource = EmployeeResource::class;

    public function getHeading(): string
    {
        return 'Edit employee';
    }

    public function getBreadcrumb(): string
    {
        return $this->record->first_name.' '.$this->record->last_name;
    }

    public function getBreadcrumbs(): array
    {
        return [
            EmployeeResource::getUrl() => 'Employees',
            $this->record->full_name => $this->record->full_name,
            'Edit',
        ];
    }
}
