<?php

namespace App\Filament\Resources\Appointments\Schemas;

use App\Models\Patient;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;

class AppointmentForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([

                Select::make('patient_id')
                    ->label('Patient')
                    ->options(
                        fn() => Patient::orderBy('last_name')
                            ->get()
                            ->mapWithKeys(fn($p) => [$p->id => $p->last_name . ' ' . $p->first_name])
                    )
                    ->searchable()
                    ->required(),

                DateTimePicker::make('start_time')
                    ->label('Heure de début')
                    ->required()
                    ->native(false)          // ← MUST be false for custom picker
                    ->seconds(false)         // hide seconds
                    ->minutesStep(30)        // only 00 and 30
                    ->displayFormat('d/m/Y H:i')
                    ->hoursStep(1),

                DateTimePicker::make('end_time')
                    ->label('Heure de fin')
                    ->required()
                    ->native(false)          // ← MUST be false
                    ->seconds(false)
                    ->minutesStep(30)
                    ->displayFormat('d/m/Y H:i')
                    ->hoursStep(1)
                    ->after('start_time'),

                Select::make('status')
                    ->label('Statut')
                    ->options([
                        'scheduled' => 'Planifié',
                        'arrived'   => 'Arrivé',
                        'completed' => 'Terminé',
                        'cancelled' => 'Annulé',
                    ])
                    ->default('scheduled')
                    ->required(),

                Textarea::make('notes')
                    ->label('Notes')
                    ->rows(3)
                    ->columnSpanFull(),

            ])
            ->columns(2);
    }
}
