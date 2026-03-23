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
                TextInput::make('cin')
                    ->label('CIN')
                    ->unique(ignoreRecord: true),
                TextInput::make('first_name')
                    ->label('Prénom')
                    ->required(),
                TextInput::make('last_name')
                    ->label('Nom')
                    ->required(),
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