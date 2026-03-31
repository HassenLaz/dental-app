<?php

namespace App\Filament\Resources\Patients;

use App\Filament\Resources\Patients\Pages;
use App\Filament\Resources\Patients\Schemas\PatientForm;
use App\Filament\Resources\Patients\Tables\PatientsTable;
use App\Models\Patient;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables\Table;

/** @noinspection PhpSignatureMismatchDuringInheritanceInspection */
class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $modelLabel = 'Patient';
    protected static ?string $pluralModelLabel = 'Patients';
    protected static ?string $slug = 'patients';

    public static function form(Form $form): Form
    {
        return PatientForm::configure($form);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Personal Information')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('first_name')->label('Prénom'),
                        TextEntry::make('last_name')->label('Nom'),
                        TextEntry::make('cin')->label('CIN'),
                        TextEntry::make('gender')->label('Genre'),
                        TextEntry::make('birthdate')->date()->label('Date de naissance'),
                        TextEntry::make('phone')->label('Téléphone'),
                        TextEntry::make('email')->label('Email'),
                    ]),
                Section::make('Medical Information')
                    ->columns(1)
                    ->schema([
                        TextEntry::make('medical_history')->placeholder("Pas d'historique médical.")->label('Antécédents médicaux'),
                        TextEntry::make('remarks')->placeholder('Pas de remarques.')->label('Remarques'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return PatientsTable::configure($table)
            ->recordUrl(fn($record) => Pages\ViewPatient::getUrl(['record' => $record]));
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPatients::route('/'),
            'create' => Pages\CreatePatient::route('/create'),
            'view'   => Pages\ViewPatient::route('/{record}'),
            'edit'   => Pages\EditPatient::route('/{record}/edit'),
        ];
    }
}
