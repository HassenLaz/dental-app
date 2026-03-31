<?php

namespace App\Filament\Resources\Appointments\Tables;

use App\Models\Appointment;
use App\Services\GoogleCalendarService;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;

class AppointmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('patient.first_name')
                    ->label('Prénom')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('patient.last_name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('start_time')
                    ->label('Début')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('end_time')
                    ->label('Fin')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'scheduled' => 'info',
                        'arrived'   => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'scheduled' => 'Planifié',
                        'arrived'   => 'Arrivé',
                        'completed' => 'Terminé',
                        'cancelled' => 'Annulé',
                        default     => $state,
                    })
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('start_time', 'asc')
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (Appointment $record) {
                        try {
                            if ($record->google_event_id) {
                                $google = app(GoogleCalendarService::class);
                                $google->deleteEvent($record->google_event_id);
                            }
                        } catch (\Exception $e) {
                            Log::warning('Google Calendar delete failed: ' . $e->getMessage());
                        }
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function (\Illuminate\Support\Collection $records) {
                            try {
                                $google = app(GoogleCalendarService::class);
                                foreach ($records as $record) {
                                    if ($record->google_event_id) {
                                        $google->deleteEvent($record->google_event_id);
                                    }
                                }
                            } catch (\Exception $e) {
                                Log::warning('Google Calendar bulk delete failed: ' . $e->getMessage());
                            }
                        }),
                ]),
            ]);
    }
}