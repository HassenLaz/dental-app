<?php

// app/Filament/Widgets/NewPatientsChartWidget.php

namespace App\Filament\Widgets;

use App\Models\Patient;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class NewPatientsChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Nouveaux patients par mois';
    protected static ?int $sort = 4;
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

        $labels = [];
        $counts = [];

        for ($m = 1; $m <= 12; $m++) {
            $start = Carbon::create($year, $m, 1)->startOfMonth();
            $end   = Carbon::create($year, $m, 1)->endOfMonth();

            $labels[] = Carbon::create($year, $m, 1)->translatedFormat('M');
            $counts[] = Patient::whereBetween('created_at', [$start, $end])->count();
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Nouveaux patients',
                    'data'            => $counts,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.7)',
                    'borderColor'     => 'rgba(59, 130, 246, 1)',
                    'borderWidth'     => 1,
                    'borderRadius'    => 6,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
