<?php

namespace App\Filament\Resources\Employees\Tables;

use App\Enums\SyncStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EmployeesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label('Full name')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query
                            ->orderBy('first_name', $direction)
                            ->orderBy('last_name', $direction);
                    }),

                TextColumn::make('email')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('job_title')
                    ->label('Job title')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('department')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('signature_sync_status')
                    ->label('Signature')
                    ->badge()

                    ->formatStateUsing(fn (SyncStatus $state) => $state->label())
                    ->color(fn (SyncStatus $state) => $state->color())
                    ->sortable()
                    ->description(function ($record) {
                        if (! $record->signature_last_synced_at) {
                            return 'Never synced';
                        }

                        $date = $record->signature_last_synced_at->setTimezone('Europe/Amsterdam');
                        $relative = $date->diffForHumans();
                        $absolute = $date->format('M j, Y H:i');

                        return "{$relative} • {$absolute}";
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make()
                    ->iconButton(),

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
