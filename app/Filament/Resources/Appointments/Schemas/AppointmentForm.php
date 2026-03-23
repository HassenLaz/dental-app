<?php

namespace App\Filament\Resources\Appointments\Schemas;

use Filament\Forms\Form;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;

class AppointmentForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('patient_id')
                    ->label('Patient')
                    ->relationship('patient', 'last_name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->first_name} {$record->last_name}")
                    ->searchable()
                    ->preload()
                    ->required(),
                DateTimePicker::make('start_time')
                    ->label('Heure de début')
                    ->required(),
                DateTimePicker::make('end_time')
                    ->label('Heure de fin')
                    ->required(),
                Select::make('status')
                    ->label('Statut')
                    ->options([
                        'scheduled' => 'Programmé',
                        'arrived' => 'En attente',
                        'completed' => 'Terminé',
                        'cancelled' => 'Annulé',
                    ])
                    ->required()
                    ->default('scheduled'),
                Textarea::make('notes')
                    ->label('Notes')
                    ->columnSpanFull(),
            ]);
    }
}