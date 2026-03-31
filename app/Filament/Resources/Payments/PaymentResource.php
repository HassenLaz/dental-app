<?php

// app/Filament/Resources/Payments/PaymentResource.php

namespace App\Filament\Resources\Payments;

use App\Filament\Resources\Payments\Pages;
use App\Models\Payment;
use App\Models\Patient;
use App\Models\TreatmentPlan;
use App\Models\Treatment;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/** @noinspection PhpSignatureMismatchDuringInheritanceInspection */

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon  = 'heroicon-o-banknotes';
    protected static ?string $modelLabel       = 'Paiement';
    protected static ?string $pluralModelLabel = 'Paiements';
    protected static ?string $slug             = 'payments';
    protected static ?int    $navigationSort   = 4;

    public static function form(Form $form): Form
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

                TextInput::make('amount')
                    ->label('Montant (TND)')
                    ->numeric()
                    ->step(0.001)
                    ->required(),

                Select::make('payment_method')
                    ->label('Méthode')
                    ->options([
                        'espèces'  => 'Espèces',
                        'chèque'   => 'Chèque',
                        'virement' => 'Virement',
                        'carte'    => 'Carte bancaire',
                    ])
                    ->default('espèces')
                    ->required(),

                DatePicker::make('paid_at')
                    ->label('Date')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->default(now())
                    ->required(),

                Textarea::make('notes')
                    ->label('Notes')
                    ->rows(2)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('patient.last_name')
                    ->label('Patient')
                    ->formatStateUsing(fn($record) => $record->patient->first_name . ' ' . $record->patient->last_name)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('payable_type')
                    ->label('Type')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'App\\Models\\TreatmentPlan' => 'Plan de traitement',
                        'App\\Models\\Treatment'     => 'Soin individuel',
                        default                      => $state,
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'App\\Models\\TreatmentPlan' => 'info',
                        'App\\Models\\Treatment'     => 'success',
                        default                      => 'gray',
                    }),

                Tables\Columns\TextColumn::make('payable.title')
                    ->label('Référence')
                    ->getStateUsing(function ($record) {
                        if ($record->payable instanceof TreatmentPlan) {
                            return $record->payable->title;
                        }
                        if ($record->payable instanceof Treatment) {
                            return $record->payable->name;
                        }
                        return '—';
                    })
                    ->limit(30),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Montant')
                    ->formatStateUsing(fn($state) => number_format($state, 3) . ' TND')
                    ->sortable()
                    ->color('success')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Méthode')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'espèces'  => 'gray',
                        'chèque'   => 'info',
                        'virement' => 'warning',
                        'carte'    => 'success',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => ucfirst($state)),

                Tables\Columns\TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(25)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('paid_at', 'desc')
            ->filters([
                SelectFilter::make('payment_method')
                    ->label('Méthode')
                    ->options([
                        'espèces'  => 'Espèces',
                        'chèque'   => 'Chèque',
                        'virement' => 'Virement',
                        'carte'    => 'Carte bancaire',
                    ]),

                SelectFilter::make('payable_type')
                    ->label('Type')
                    ->options([
                        'App\\Models\\TreatmentPlan' => 'Plan de traitement',
                        'App\\Models\\Treatment'     => 'Soin individuel',
                    ]),

                Filter::make('this_month')
                    ->label('Ce mois-ci')
                    ->query(fn(Builder $query) => $query->whereMonth('paid_at', now()->month)
                        ->whereYear('paid_at', now()->year)),

                Filter::make('this_year')
                    ->label('Cette année')
                    ->query(fn(Builder $query) => $query->whereYear('paid_at', now()->year)),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            // Summary footer showing total amount
            ->heading(function () {
                $total = Payment::sum('amount');
                $thisMonth = Payment::whereMonth('paid_at', now()->month)
                    ->whereYear('paid_at', now()->year)
                    ->sum('amount');
                return null; // heading handled by resource label
            });
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit'   => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
