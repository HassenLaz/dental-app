<?php

namespace App\Filament\Resources\Treatments;

use App\Filament\Resources\Treatments\Pages;
use App\Filament\Resources\Treatments\Schemas\TreatmentForm;
use App\Models\Treatment;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TreatmentResource extends Resource
{
    protected static ?string $model = Treatment::class;

    // Use a simple string for the icon to match parent class
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $modelLabel = 'Soin';
    protected static ?string $pluralModelLabel = 'Soins';

    protected static ?string $slug = 'treatments';

    public static function form(Form $form): Form
    {
        return TreatmentForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('patient.last_name')
                    ->label('Patient')
                    ->formatStateUsing(fn ($record) => "{$record->patient->first_name} {$record->patient->last_name}")
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Acte')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tooth_number')
                    ->label('Dent'),
                Tables\Columns\TextColumn::make('cost')
                    ->label('Prix')
                    ->money('TND'),
                Tables\Columns\SelectColumn::make('payment_status')
                    ->label('Paiement')
                    ->options([
                        'pending' => 'En attente',
                        'partial' => 'Partiel',
                        'paid' => 'Payé',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options([
                        'pending' => 'En attente',
                        'paid' => 'Payé',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTreatments::route('/'),
            'create' => Pages\CreateTreatment::route('/create'),
            'edit' => Pages\EditTreatment::route('/{record}/edit'),
        ];
    }
}