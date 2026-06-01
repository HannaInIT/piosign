<?php

namespace App\Enums;

enum SyncStatus: int
{
    case Pending = 0;
    case Synced = 1;
    case Failed = 2;

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Synced => 'Synced',
            self::Failed => 'Failed',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Synced => 'success',
            self::Failed => 'danger',
        };
    }
}
