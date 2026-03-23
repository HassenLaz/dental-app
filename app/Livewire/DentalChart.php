<?php

// app/Livewire/DentalChart.php

namespace App\Livewire;

use App\Models\Treatment;
use Livewire\Component;
use Illuminate\Support\Collection;

class DentalChart extends Component
{
    public int $patientId;

    public ?string $selectedToothNumber = null;

    public array $form = [
        'name'           => '',
        'description'    => '',
        'cost'           => '',
        'amount_paid'    => '0',
        'payment_status' => 'pending',
        'date'           => '',
    ];

    // All tooth numbers that already have at least one treatment
    public array $treatedTeeth = [];

    public Collection $toothTreatments;
    public Collection $allTreatments;

    public function mount(int $patientId): void
    {
        $this->patientId       = $patientId;
        $this->toothTreatments = collect();
        $this->allTreatments   = collect();
        $this->form['date']    = now()->toDateString();
        $this->refreshData();
    }

    public function selectTooth(string $toothNumber): void
    {
        $this->selectedToothNumber = $toothNumber;
        $this->toothTreatments = Treatment::where('patient_id', $this->patientId)
            ->where('tooth_number', $toothNumber)
            ->orderByDesc('date')
            ->get();

        // Reset form but keep date
        $this->form = [
            'name'           => '',
            'description'    => '',
            'cost'           => '',
            'amount_paid'    => '0',
            'payment_status' => 'pending',
            'date'           => now()->toDateString(),
        ];

        $this->resetValidation();
    }

    public function clearSelection(): void
    {
        $this->selectedToothNumber = null;
        $this->toothTreatments     = collect();
        $this->resetValidation();
    }

    public function saveTreatment(): void
    {
        $this->validate([
            'form.name' => 'required|string|max:255',
            'form.cost' => 'required|numeric|min:0',
            'form.date' => 'required|date',
            'form.amount_paid'    => 'nullable|numeric|min:0',
            'form.payment_status' => 'required|in:pending,partial,paid',
        ]);

        Treatment::create([
            'patient_id'     => $this->patientId,
            'tooth_number'   => $this->selectedToothNumber,
            'name'           => $this->form['name'],
            'description'    => $this->form['description'] ?: null,
            'cost'           => $this->form['cost'],
            'amount_paid'    => $this->form['amount_paid'] ?: 0,
            'payment_status' => $this->form['payment_status'],
            'date'           => $this->form['date'],
        ]);

        $this->refreshData();

        // Reload treatments for this tooth
        $this->toothTreatments = Treatment::where('patient_id', $this->patientId)
            ->where('tooth_number', $this->selectedToothNumber)
            ->orderByDesc('date')
            ->get();

        // Reset form
        $this->form = [
            'name'           => '',
            'description'    => '',
            'cost'           => '',
            'amount_paid'    => '0',
            'payment_status' => 'pending',
            'date'           => now()->toDateString(),
        ];

        $this->dispatch('treatment-saved');
        session()->flash('success', 'Treatment saved successfully.');
    }

    protected function refreshData(): void
    {
        $this->allTreatments = Treatment::where('patient_id', $this->patientId)
            ->orderByDesc('date')
            ->get();

        $this->treatedTeeth = Treatment::where('patient_id', $this->patientId)
            ->whereNotNull('tooth_number')
            ->pluck('tooth_number')
            ->unique()
            ->values()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.dental-chart');
    }
}
