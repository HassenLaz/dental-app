<?php

namespace App\Filament\Resources\Treatments\Schemas;

use Filament\Forms\Form; // Use Form
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;

class TreatmentForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([ // Use ->schema() instead of ->components()
                Select::make('patient_id')
                    ->relationship('patient', 'last_name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->first_name} {$record->last_name}")
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Patient'),

                Select::make('tooth_number')
                    ->label('N° de Dent')
                    ->options([
                        '11' => '11', '12' => '12', '13' => '13', '14' => '14', '15' => '15', '16' => '16', '17' => '17', '18' => '18',
                        '21' => '21', '22' => '22', '23' => '23', '24' => '24', '25' => '25', '26' => '26', '27' => '27', '28' => '28',
                        '31' => '31', '32' => '32', '33' => '33', '34' => '34', '35' => '35', '36' => '36', '37' => '37', '38' => '38',
                        '41' => '41', '42' => '42', '43' => '43', '44' => '44', '45' => '45', '46' => '46', '47' => '47', '48' => '48',
                    ])
                    ->searchable(),

                TextInput::make('name')
                    ->label('Acte / Soin')
                    ->required(),

                Textarea::make('description')
                    ->label('Description')
                    ->columnSpanFull(),

                TextInput::make('cost')
                    ->label('Coût')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('DT'),

                TextInput::make('amount_paid')
                    ->label('Montant Payé')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('DT'),

                Select::make('payment_status')
                    ->label('État de Paiement')
                    ->options([
                        'pending' => 'En attente',
                        'partial' => 'Partiel',
                        'paid' => 'Payé',
                    ])
                    ->required()
                    ->default('pending'),

                DatePicker::make('date')
                    ->label('Date')
                    ->default(now())
                    ->required(),
            ]);
    }
}