<?php

// app/Filament/Widgets/AppointmentStatusWidget.php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class AppointmentStatusWidget extends ChartWidget
{
    protected static ?string $heading = 'Rendez-vous ce mois';
    protected static ?int $sort = 5;
    protected static ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $start = Carbon::now()->startOfMonth();
        $end   = Carbon::now()->endOfMonth();

        $statuses = [
            'scheduled' => ['label' => 'Planifié',  'color' => 'rgba(99, 102, 241, 0.85)'],
            'arrived'   => ['label' => 'Arrivé',    'color' => 'rgba(59, 130, 246, 0.85)'],
            'completed' => ['label' => 'Terminé',   'color' => 'rgba(34, 197, 94, 0.85)'],
            'cancelled' => ['label' => 'Annulé',    'color' => 'rgba(239, 68, 68, 0.85)'],
        ];

        $data   = [];
        $labels = [];
        $colors = [];

        foreach ($statuses as $key => $info) {
            $count = Appointment::whereBetween('start_time', [$start, $end])
                ->where('status', $key)
                ->count();

            if ($count > 0) {
                $data[]   = $count;
                $labels[] = $info['label'];
                $colors[] = $info['color'];
            }
        }

        // If no appointments, show placeholder
        if (empty($data)) {
            $data   = [1];
            $labels = ['Aucun rendez-vous'];
            $colors = ['rgba(75, 85, 99, 0.5)'];
        }

        return [
            'datasets' => [
                [
                    'data'            => $data,
                    'backgroundColor' => $colors,
                    'borderWidth'     => 0,
                    'hoverOffset'     => 6,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
