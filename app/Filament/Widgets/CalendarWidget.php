<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarWidget extends FullCalendarWidget
{
    public function fetchEvents(array $fetchInfo): array
    {
        $colors = [
            'scheduled' => ['bg' => '#6366f1', 'border' => '#4f46e5'],
            'arrived'   => ['bg' => '#3b82f6', 'border' => '#2563eb'],
            'completed' => ['bg' => '#22c55e', 'border' => '#16a34a'],
            'cancelled' => ['bg' => '#ef4444', 'border' => '#dc2626'],
        ];

        $statusLabels = [
            'scheduled' => 'Planifié',
            'arrived'   => 'Arrivé',
            'completed' => 'Terminé',
            'cancelled' => 'Annulé',
        ];

        return Appointment::query()
            ->with('patient')
            ->where('start_time', '>=', $fetchInfo['start'])
            ->where('end_time', '<=', $fetchInfo['end'])
            ->get()
            ->map(function (Appointment $appointment) use ($colors, $statusLabels) {
                $status = $appointment->status ?? 'scheduled';
                $color  = $colors[$status] ?? $colors['scheduled'];
                $label  = $statusLabels[$status] ?? ucfirst($status);

                return [
                    'id'              => $appointment->id,
                    'title'           => $appointment->patient->first_name . ' ' . $appointment->patient->last_name . " | \n" . $label,
                    'start'           => $appointment->start_time,
                    'end'             => $appointment->end_time,
                    'url'             => route('filament.admin.resources.appointments.edit', ['record' => $appointment]),
                    'backgroundColor' => $color['bg'],
                    'borderColor'     => $color['border'],
                    'textColor'       => '#ffffff',
                ];
            })
            ->toArray();
    }

    public function config(): array
    {
        return [
            'headerToolbar' => [
                'left'   => 'prev,next today',
                'center' => 'title',
                'right'  => 'dayGridMonth,timeGridWeek,timeGridDay',
            ],
            'initialView'   => 'timeGridWeek',
            'nowIndicator'  => true,
            'allDaySlot'    => false,
            'slotMinTime'   => '08:00:00',
            'slotMaxTime'   => '21:00:00',
            'eventDisplay'  => 'block',
            'locale'        => 'fr',          // French locale = 24h + French day/month names
            'timeZone'      => 'Africa/Tunis',
        ];
    }
}
