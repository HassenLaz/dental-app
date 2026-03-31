<?php

// app/Filament/Widgets/PaymentStatusWidget.php

namespace App\Filament\Widgets;

use App\Models\Payment;
use App\Models\Treatment;
use App\Models\TreatmentPlan;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class PaymentStatusWidget extends ChartWidget
{
    protected static ?string $heading   = 'Paiements par type';
    protected static ?int    $sort      = 5;
    protected static ?string $maxHeight = '280px';

    public ?string $filter = 'month';

    protected function getFilters(): ?array
    {
        return [
            'month' => 'Ce mois',
            'year'  => 'Cette année',
            'all'   => 'Tout',
        ];
    }

    protected function getData(): array
    {
        $query = Payment::query();

        if ($this->filter === 'month') {
            $query->where('paid_at', '>=', Carbon::now()->startOfMonth());
        } elseif ($this->filter === 'year') {
            $query->where('paid_at', '>=', Carbon::now()->startOfYear());
        }

        $fromTreatments = (clone $query)->where('payable_type', Treatment::class)->sum('amount');
        $fromPlans      = (clone $query)->where('payable_type', TreatmentPlan::class)->sum('amount');
        $total          = $fromTreatments + $fromPlans;

        if ($total == 0) {
            return [
                'datasets' => [[
                    'data'            => [1],
                    'backgroundColor' => ['rgba(75, 85, 99, 0.5)'],
                    'borderWidth'     => 0,
                ]],
                'labels' => ['Aucun paiement'],
            ];
        }

        return [
            'datasets' => [[
                'data'            => [round($fromTreatments, 3), round($fromPlans, 3)],
                'backgroundColor' => [
                    'rgba(245, 158, 11, 0.85)',  // amber - treatments
                    'rgba(34, 197, 94, 0.85)',   // green - plans
                ],
                'borderWidth' => 0,
                'hoverOffset' => 6,
            ]],
            'labels' => [
                'Soins individuels (' . number_format($fromTreatments, 3) . ' TND)',
                'Plans de traitement (' . number_format($fromPlans, 3) . ' TND)',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
