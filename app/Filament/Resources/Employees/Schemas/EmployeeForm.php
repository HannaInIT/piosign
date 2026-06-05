<?php

namespace App\Filament\Resources\Employees\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Grid::make(1)
                    ->columnSpan(1)
                    ->schema([
                        Section::make('Edit employee information')
                            ->schema([
                                TextInput::make('job_title')
                                    ->live(onBlur: true),
                                TextInput::make('department')
                                    ->live(onBlur: true),
                                TextInput::make('phone_number')
                                    ->tel()
                                    ->live(onBlur: true),
                            ]),
                        Section::make('Employee info')
                            ->description('Synced from Google Workspace')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('uuid')
                                            ->label('UUID')
                                            ->disabled()
                                            ->columnSpanFull(),

                                        TextInput::make('google_id')
                                            ->label('Google ID')
                                            ->disabled(),

                                        TextInput::make('email')
                                            ->disabled()
                                            ->dehydrated(false),

                                        TextInput::make('first_name')
                                            ->disabled()
                                            ->dehydrated(false),

                                        TextInput::make('last_name')
                                            ->disabled()
                                            ->dehydrated(false),
                                    ]),
                            ]),
                    ]),

                Section::make('Signature preview')
                    ->columnSpan(1)
                    ->extraAlpineAttributes(['class' => 'signature-sticky'])
                    ->headerActions([
                        Action::make('sync_status')
                            ->label(fn ($record) => $record?->signature_sync_status?->label() ?? 'Unknown')
                            ->color(fn ($record) => $record?->signature_sync_status?->color() ?? 'gray')
                            ->badge()
                            ->disabled(),

                        Action::make('synced_at')
                            ->label(function ($record) {
                                if (! $record?->signature_last_synced_at) {
                                    return 'Never synced';
                                }

                                $date = $record->signature_last_synced_at->setTimezone('Europe/Amsterdam');

                                return $date->diffForHumans().' • '.$date->format('M j, Y H:i');
                            })
                            ->color('gray')
                            ->link()
                            ->disabled(),
                    ])
                    ->schema([
                        View::make('components.signature'),
                    ]),
            ]);
    }
}
