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

        return Appointment::query()
            ->with('patient')
            ->where('start_time', '>=', $fetchInfo['start'])
            ->where('end_time', '<=', $fetchInfo['end'])
            ->get()
            ->map(function (Appointment $appointment) use ($colors) {
                $status = $appointment->status ?? 'scheduled';
                $color  = $colors[$status] ?? $colors['scheduled'];

                // Format: "11:30 Hassen L."
                $startTime   = \Carbon\Carbon::parse($appointment->start_time)->format('H:i');
                // $patientName = strtoupper(substr($appointment->patient->first_name, 0, 1)) . ' ' . $appointment->patient->last_name . '.';
                $patientName = $appointment->patient->last_name . ' ' . strtoupper(substr($appointment->patient->first_name, 0, 1)) . '.';
                return [
                    'id'              => $appointment->id,
                    'title'           => $startTime . ', ' . $patientName,
                    'start'           => $appointment->start_time,
                    'end'             => $appointment->end_time,
                    'url'             => route('filament.admin.resources.appointments.edit', ['record' => $appointment]),
                    'backgroundColor' => $color['bg'],
                    'borderColor'     => $color['border'],
                    'textColor'       => '#ffffff',
                    'extendedProps'   => [
                        'status' => $status,
                        'phone'  => $appointment->patient->phone ?? '',
                    ],
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
            'initialView'        => 'timeGridWeek',
            'nowIndicator'       => true,
            'allDaySlot'         => false,
            'slotMinTime'        => '08:00:00',
            'slotMaxTime'        => '21:00:00',
            'locale'             => 'fr',
            'timeZone'           => 'Africa/Tunis',
            'slotDuration'       => '00:30:00',   // 30-min grid lines
            'slotLabelInterval'  => '01:00:00',   // label every hour only
            'slotEventOverlap'   => false,
            'eventDisplay'       => 'block',
            'displayEventTime'   => false,        // hide the redundant time FullCalendar adds
            'eventMinHeight'     => 28,           // minimum slot height in px
            'expandRows'         => true,         // expand rows to fill height
            'hiddenDays' => [0],
            
            // Custom event rendering — shows only what fits
            'eventDidMount' => 'function(info) {
                var el = info.el.querySelector(".fc-event-title");
                if (el) {
                    el.style.fontSize = "12px";
                    el.style.fontWeight = "600";
                    el.style.overflow = "hidden";
                    el.style.whiteSpace = "nowrap";
                    el.style.textOverflow = "ellipsis";
                    el.style.padding = "2px 4px";
                }
            }',
        ];
    }
}
