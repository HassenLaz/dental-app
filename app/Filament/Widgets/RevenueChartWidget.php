<?php

// ═══════════════════════════════════════════════════════════════
// app/Filament/Widgets/RevenueChartWidget.php
// ═══════════════════════════════════════════════════════════════

namespace App\Filament\Widgets;

use App\Models\Payment;
use App\Models\Treatment;
use App\Models\TreatmentPlan;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RevenueChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Encaissements mensuels (TND)';
    protected static ?int    $sort    = 2;
    protected static ?string $maxHeight = '280px';

    public ?string $filter = 'year';

    protected function getFilters(): ?array
    {
        return [
            'year'     => 'Cette année',
            'lastyear' => 'Année dernière',
        ];
    }

    protected function getData(): array
    {
        $year = $this->filter === 'lastyear'
            ? Carbon::now()->subYear()->year
            : Carbon::now()->year;

        $labels        = [];
        $fromTreatments = [];
        $fromPlans     = [];

        for ($m = 1; $m <= 12; $m++) {
            $start = Carbon::create($year, $m, 1)->startOfMonth();
            $end   = Carbon::create($year, $m, 1)->endOfMonth();

            $labels[] = Carbon::create($year, $m, 1)->translatedFormat('M');

            // Payments linked to standalone treatments
            $fromTreatments[] = round(
                Payment::whereBetween('paid_at', [$start, $end])
                    ->where('payable_type', Treatment::class)
                    ->sum('amount'),
                3
            );

            // Payments linked to treatment plans
            $fromPlans[] = round(
                Payment::whereBetween('paid_at', [$start, $end])
                    ->where('payable_type', TreatmentPlan::class)
                    ->sum('amount'),
                3
            );
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Soins individuels',
                    'data'            => $fromTreatments,
                    'backgroundColor' => 'rgba(245, 158, 11, 0.15)',
                    'borderColor'     => 'rgba(245, 158, 11, 1)',
                    'borderWidth'     => 2,
                    'fill'            => true,
                    'tension'         => 0.4,
                ],
                [
                    'label'           => 'Plans de traitement',
                    'data'            => $fromPlans,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.15)',
                    'borderColor'     => 'rgba(34, 197, 94, 1)',
                    'borderWidth'     => 2,
                    'fill'            => true,
                    'tension'         => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
