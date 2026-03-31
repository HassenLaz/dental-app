<?php

namespace App\Filament\Resources\Appointments;

use App\Filament\Resources\Appointments\Pages;
use App\Filament\Resources\Appointments\Schemas\AppointmentForm;
use App\Filament\Resources\Appointments\Tables\AppointmentsTable;
use App\Models\Appointment;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

/** @noinspection PhpSignatureMismatchDuringInheritanceInspection */
class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $modelLabel = 'Rendez-vous';
    protected static ?string $pluralModelLabel = 'Rendez-vous';
    protected static ?string $slug = 'appointments';

    
    public static function form(Form $form): Form
    {
        return AppointmentForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return AppointmentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'edit'   => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }
}