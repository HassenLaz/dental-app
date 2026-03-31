<?php

// app/Filament/Widgets/GoogleCalendarStatusWidget.php

namespace App\Filament\Widgets;

use App\Services\GoogleCalendarService;
use Filament\Widgets\Widget;

class GoogleCalendarStatusWidget extends Widget
{
    protected static string $view = 'filament.widgets.google-calendar-status';
    protected static ?int $sort = 6;

    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array  // ← protected, not public
    {
        $google = app(GoogleCalendarService::class);
        return [
            'connected' => $google->isAuthenticated(),
        ];
    }
}