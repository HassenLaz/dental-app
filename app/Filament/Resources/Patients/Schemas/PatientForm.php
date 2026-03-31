<?php

namespace App\Filament\Resources\Patients\Schemas;

use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;

class PatientForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('first_name')
                    ->label('Prénom')
                    ->required(),
                TextInput::make('last_name')
                    ->label('Nom')
                    ->required(),
                TextInput::make('cin')
                    ->label('CIN')
                    ->unique(ignoreRecord: true),
                DatePicker::make('birthdate')
                    ->label('Date de naissance')
                    ->required(),
                Select::make('gender')
                    ->label('Genre')
                    ->options([
                        'M' => 'Masculin',
                        'F' => 'Féminin',
                    ])
                    ->required(),
                TextInput::make('phone')
                    ->label('Téléphone')
                    ->tel()
                    ->required(),
                DatePicker::make('created_at')
                    ->label('Date de première visite')
                    ->displayFormat('d/m/Y')
                    ->native(false)
                    ->default(now())
                    ->required()
                    ->columnSpan(1),
                TextInput::make('num_record')
                    ->label('Numéro de fiche')
                    ->numeric()
                    ->integer()                    
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->minValue(1)
                    ->columnSpan(1),
                TextInput::make('email')
                    ->label('Email')
                    ->email(),
                Textarea::make('medical_history')
                    ->label('Antécédents médicaux')
                    ->columnSpanFull(),
                Textarea::make('remarks')
                    ->label('Remarques')
                    ->columnSpanFull(),
            ]);
    }
}
