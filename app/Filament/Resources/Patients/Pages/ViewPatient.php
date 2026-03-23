<?php

// app/Filament/Resources/PatientResource/Pages/ViewPatient.php
// (adjust the namespace to match your PatientResource location)

namespace App\Filament\Resources\Patients\Pages;


use App\Filament\Resources\Patients\PatientResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPatient extends ViewRecord
{
    protected static string $resource = PatientResource::class;
    protected static string $view = 'filament.resources.patient-resource.pages.view-patient';

    // The view below embeds the Livewire dental chart under Filament's
    // standard view layout. Override getView() to use a custom blade file.
    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
