<?php

// app/Filament/Widgets/StatsOverviewWidget.php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Treatment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $today        = Carbon::today();
        $thisMonth    = Carbon::now()->startOfMonth();
        $lastMonth    = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // Today's appointments
        $todayCount = Appointment::whereDate('start_time', $today)->count();

        // This month's appointments vs last month
        $monthCount       = Appointment::where('start_time', '>=', $thisMonth)->count();
        $lastMonthCount   = Appointment::whereBetween('start_time', [$lastMonth, $lastMonthEnd])->count();
        $appointmentTrend = $lastMonthCount > 0
            ? round((($monthCount - $lastMonthCount) / $lastMonthCount) * 100, 1)
            : 0;

        // Total patients
        $totalPatients = Patient::count();
        $newThisMonth  = Patient::where('created_at', '>=', $thisMonth)->count();

        // Revenue from payments this month (treatments + plans combined)
        $monthRevenue     = Payment::where('paid_at', '>=', $thisMonth)->sum('amount');
        $lastMonthRevenue = Payment::whereBetween('paid_at', [$lastMonth, $lastMonthEnd])->sum('amount');
        $revenueTrend     = $lastMonthRevenue > 0
            ? round((($monthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : 0;

        // Outstanding = sum of (cost - paid) for standalone treatments with unpaid balance
        $outstanding = Treatment::standalone()
            ->whereNotNull('cost')
            ->get()
            ->sum(fn($t) => $t->remainingBalance());

        // Cancellation rate
        $cancelledCount   = Appointment::where('start_time', '>=', $thisMonth)->where('status', 'cancelled')->count();
        $cancellationRate = $monthCount > 0 ? round(($cancelledCount / $monthCount) * 100, 1) : 0;

        return [
            Stat::make("Rendez-vous aujourd'hui", $todayCount)
                ->description('Consultations du jour')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),

            Stat::make('Rendez-vous ce mois', $monthCount)
                ->description(($appointmentTrend >= 0 ? '+' : '') . $appointmentTrend . '% vs mois dernier')
                ->descriptionIcon($appointmentTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($appointmentTrend >= 0 ? 'success' : 'danger'),

            Stat::make("Taux d'annulation", $cancellationRate . '%')
                ->description($cancelledCount . ' annulé(s) sur ' . $monthCount . ' ce mois')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color($cancellationRate > 20 ? 'danger' : ($cancellationRate > 10 ? 'warning' : 'success')),
                
            Stat::make('Patients', number_format($totalPatients))
                ->description('+' . $newThisMonth . ' nouveau(x) ce mois')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('success'),

            Stat::make('Encaissé ce mois', number_format($monthRevenue, 3) . ' TND')
                ->description(($revenueTrend >= 0 ? '+' : '') . $revenueTrend . '% vs mois dernier')
                ->descriptionIcon($revenueTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueTrend >= 0 ? 'success' : 'danger'),

            Stat::make('Solde impayé', number_format($outstanding, 3) . ' TND')
                ->description('Soins individuels non soldés')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($outstanding > 0 ? 'warning' : 'success'),


        ];
    }
}
