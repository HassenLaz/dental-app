<?php

// app/Filament/Resources/Treatments/TreatmentResource.php

namespace App\Filament\Resources\Treatments;

use App\Filament\Resources\Treatments\Pages;
use App\Models\Treatment;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TreatmentResource extends Resource
{
    protected static ?string $model = Treatment::class;

    protected static ?string $navigationIcon  = 'heroicon-o-clipboard-document-check';
    protected static ?string $modelLabel       = 'Soin';
    protected static ?string $pluralModelLabel = 'Soins';
    protected static ?string $slug             = 'treatments';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('patient_id')
                    ->relationship('patient', 'last_name')
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->first_name} {$record->last_name}")
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Patient'),

                Select::make('tooth_number')
                    ->label('N° de dent')
                    ->options(
                        collect(range(11, 18))
                            ->concat(range(21, 28))
                            ->concat(range(31, 38))
                            ->concat(range(41, 48))
                            ->mapWithKeys(fn($n) => [(string) $n => (string) $n])
                    )
                    ->searchable(),

                TextInput::make('name')
                    ->label('Acte / Soin')
                    ->required(),

                Select::make('status')
                    ->label('Statut')
                    ->options([
                        'standalone' => 'Individuel',
                        'planned'    => 'Planifié',
                        'completed'  => 'Effectué',
                        'cancelled'  => 'Annulé',
                    ])
                    ->default('standalone')
                    ->required(),

                TextInput::make('cost')
                    ->label('Coût (TND)')
                    ->numeric()
                    ->step(0.001)
                    ->prefix('DT'),

                DatePicker::make('date')
                    ->label('Date')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->default(now()),

                Textarea::make('description')
                    ->label('Description')
                    ->rows(2)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('patient.last_name')
                    ->label('Patient')
                    ->formatStateUsing(fn($record) => $record->patient->first_name . ' ' . $record->patient->last_name)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Acte')
                    ->searchable(),

                Tables\Columns\TextColumn::make('tooth_number')
                    ->label('Dent'),

                // ── Type column (replaces Statut) ──────────────────
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->getStateUsing(fn($record): string => $record->treatment_plan_id ? 'plan' : 'standalone')
                    ->color(fn(string $state): string => match ($state) {
                        'plan'       => 'info',
                        'standalone' => 'gray',
                        default      => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'plan'       => 'Plan',
                        'standalone' => 'Individuel',
                        default      => $state,
                    }),

                // ── Cost column — shows individual cost or plan total ──
                Tables\Columns\TextColumn::make('cost')
                    ->label('Coût')
                    ->getStateUsing(function ($record): string {
                        if ($record->treatment_plan_id === null) {
                            // Standalone — show own cost
                            return $record->cost
                                ? number_format($record->cost, 3) . ' TND'
                                : '—';
                        }

                        // Part of a plan — show plan total with indicator
                        $planCost = $record->plan?->total_cost;
                        return $planCost
                            ? number_format($planCost, 3) . ' TND *'
                            : '—';
                    })
                    ->sortable()
                    ->tooltip(
                        fn($record): ?string =>
                        $record->treatment_plan_id
                            ? '* Coût total du plan "' . ($record->plan?->title ?? 'Plan') . '"'
                            : null
                    ),

                // ── Paid — only meaningful for standalone ──────────
                Tables\Columns\TextColumn::make('total_paid')
                    ->label('Payé')
                    ->getStateUsing(function ($record): string {
                        if ($record->treatment_plan_id) {
                            // Show how much has been paid toward the plan
                            $paid = $record->plan?->amount_paid ?? 0;
                            return number_format($paid, 3) . ' TND *';
                        }
                        return number_format($record->totalPaid(), 3) . ' TND';
                    })
                    ->tooltip(
                        fn($record): ?string =>
                        $record->treatment_plan_id
                            ? '* Montant total payé pour le plan entier'
                            : null
                    ),

                // ── Payment status ─────────────────────────────────
                Tables\Columns\TextColumn::make('payment_status_computed')
                    ->label('Paiement')
                    ->badge()
                    ->getStateUsing(function ($record): string {
                        if ($record->treatment_plan_id) {
                            return $record->plan?->payment_status ?? 'pending';
                        }
                        return $record->paymentStatus();
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'paid'    => 'success',
                        'partial' => 'warning',
                        default   => 'danger',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'paid'    => 'Payé',
                        'partial' => 'Partiel',
                        default   => 'En attente',
                    }),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'standalone' => 'Individuel',
                        'plan'       => 'Plan de traitement',
                    ])
                    ->query(function ($query, array $data) {
                        if ($data['value'] === 'standalone') {
                            $query->whereNull('treatment_plan_id');
                        } elseif ($data['value'] === 'plan') {
                            $query->whereNotNull('treatment_plan_id');
                        }
                    }),

                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('Paiement')
                    ->options([
                        'pending' => 'En attente',
                        'partial' => 'Partiel',
                        'paid'    => 'Payé',
                    ])
                    ->query(function ($query, array $data) {
                        if (empty($data['value'])) return;
                        // For standalone: filter by computed status via payments
                        // For plan items: filter by plan's payment_status
                        $query->where(function ($q) use ($data) {
                            // Standalone treatments — match computed payment status
                            $q->whereNull('treatment_plan_id')
                                ->whereHas('payments', fn($p) => $p, '>=', 0); // all

                            // Plan items — match plan's payment_status
                            $q->orWhereHas(
                                'plan',
                                fn($p) =>
                                $p->where('payment_status', $data['value'])
                            );
                        });
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTreatments::route('/'),
            'create' => Pages\CreateTreatment::route('/create'),
            'edit'   => Pages\EditTreatment::route('/{record}/edit'),
        ];
    }
}
