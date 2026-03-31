<?php

// app/Filament/Pages/Dashboard.php
// This overrides Filament's default dashboard to show role-specific widgets.

namespace App\Filament\Pages;

use App\Filament\Widgets\CalendarWidget;
use App\Filament\Widgets\GoogleCalendarStatusWidget;
use App\Filament\Widgets\StatsOverviewWidget;
use App\Filament\Widgets\RevenueChartWidget;
use App\Filament\Widgets\NewPatientsChartWidget;
use App\Filament\Widgets\AppointmentStatusWidget;
use App\Filament\Widgets\PaymentStatusWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        $user = auth()->user();

        if ($user->isReceptionist()) {
            // Receptionist sees only the calendar
            return [
                GoogleCalendarStatusWidget::class,
                CalendarWidget::class,
            ];
        }

        // Dentist sees everything
        return [
            CalendarWidget::class,
            GoogleCalendarStatusWidget::class,
            StatsOverviewWidget::class,
            RevenueChartWidget::class,
            NewPatientsChartWidget::class,
            AppointmentStatusWidget::class,
            PaymentStatusWidget::class,
        ];
    }

    public function getColumns(): int | array
    {
        // Full width for receptionist (just calendar), 2-col grid for dentist
        if (auth()->user()->isReceptionist()) {
            return 1;
        }

        return 2;
    }
}