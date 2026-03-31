<?php
// app/Filament/Resources/Payments/Pages/ListPayments.php

namespace App\Filament\Resources\Payments\Pages;

use App\Filament\Resources\Payments\PaymentResource;
use App\Models\Payment;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    // Show total received at the top of the page
    protected function getHeaderWidgets(): array
    {
        return [];
    }
}
