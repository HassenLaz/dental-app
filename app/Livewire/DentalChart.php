<?php

// app/Livewire/DentalChart.php

namespace App\Livewire;

use App\Models\Payment;
use App\Models\Treatment;
use Livewire\Component;
use Illuminate\Support\Collection;

class DentalChart extends Component
{
    public int $patientId;

    public ?string $selectedToothNumber = null;

    // Add treatment form
    public array $form = [
        'name'        => '',
        'description' => '',
        'cost'        => '',
        'date'        => '',
    ];

    // Autocomplete
    public array $suggestions    = [];
    public bool  $showSuggestions = false;

    // Tooth data
    public array      $treatedTeeth    = [];
    public Collection $toothTreatments;
    public Collection $allTreatments;

    // Payment modal state
    public ?int  $payingTreatmentId = null;
    public bool  $showPaymentModal  = false;
    public array $paymentForm       = [
        'amount'         => '',
        'payment_method' => 'espèces',
        'paid_at'        => '',
        'notes'          => '',
    ];

    // Delete confirmation
    public ?int $confirmDeleteTreatmentId = null;

    protected array $dentalActs = [];

    public function mount(int $patientId): void
    {
        $this->patientId       = $patientId;
        $this->toothTreatments = collect();
        $this->allTreatments   = collect();
        $this->form['date']    = now()->toDateString();
        $this->paymentForm['paid_at'] = now()->toDateString();
        $this->loadDentalActs();
        $this->refreshData();
    }

    protected function loadDentalActs(): void
    {
        $path = storage_path('app/dental-acts.json');
        if (file_exists($path)) {
            $this->dentalActs = json_decode(file_get_contents($path), true) ?? [];
        }
    }

    // ─── Autocomplete ──────────────────────────────────────────────

    public function updatedFormName(string $value): void
    {
        $value = trim($value);
        if (strlen($value) < 2) {
            $this->suggestions     = [];
            $this->showSuggestions = false;
            return;
        }

        $this->loadDentalActs();

        $this->suggestions = collect($this->dentalActs)
            ->filter(fn($act) => str_contains(mb_strtolower($act['name']), mb_strtolower($value)))
            ->values()
            ->take(6)
            ->toArray();

        $this->showSuggestions = count($this->suggestions) > 0;
    }

    public function selectSuggestion(string $name, float $cost): void
    {
        $this->form['name']    = $name;
        $this->form['cost']    = $cost;
        $this->suggestions     = [];
        $this->showSuggestions = false;
    }

    public function hideSuggestions(): void
    {
        $this->showSuggestions = false;
        $this->suggestions     = [];
    }

    // ─── Tooth selection ───────────────────────────────────────────

    public function selectTooth(string $toothNumber): void
    {
        $this->selectedToothNumber = $toothNumber;
        $this->loadToothTreatments();

        $this->form = [
            'name'        => '',
            'description' => '',
            'cost'        => '',
            'date'        => now()->toDateString(),
        ];

        $this->suggestions              = [];
        $this->showSuggestions          = false;
        $this->confirmDeleteTreatmentId = null;
        $this->resetValidation();
    }

    public function clearSelection(): void
    {
        $this->selectedToothNumber      = null;
        $this->toothTreatments          = collect();
        $this->suggestions              = [];
        $this->showSuggestions          = false;
        $this->confirmDeleteTreatmentId = null;
        $this->resetValidation();
    }

    protected function loadToothTreatments(): void
    {
        $this->toothTreatments = Treatment::where('patient_id', $this->patientId)
            ->where('tooth_number', $this->selectedToothNumber)
            ->whereNull('treatment_plan_id') // only standalone treatments
            ->with('payments')
            ->orderByDesc('date')
            ->get();
    }

    // ─── Save Treatment ────────────────────────────────────────────

    public function saveTreatment(): void
    {
        $this->validate([
            'form.name' => 'required|string|max:255',
            'form.cost' => 'required|numeric|min:0',
            'form.date' => 'required|date',
        ]);

        Treatment::create([
            'patient_id'        => $this->patientId,
            'treatment_plan_id' => null,
            'tooth_number'      => $this->selectedToothNumber,
            'name'              => $this->form['name'],
            'description'       => $this->form['description'] ?: null,
            'cost'              => $this->form['cost'],
            'status'            => 'standalone',
            'date'              => $this->form['date'],
        ]);

        $this->refreshData();
        $this->loadToothTreatments();

        $this->form = [
            'name'        => '',
            'description' => '',
            'cost'        => '',
            'date'        => now()->toDateString(),
        ];

        $this->suggestions     = [];
        $this->showSuggestions = false;
    }

    // ─── Delete Treatment ──────────────────────────────────────────

    public function confirmDeleteTreatment(int $id): void
    {
        $this->confirmDeleteTreatmentId = $id;
    }

    public function cancelDeleteTreatment(): void
    {
        $this->confirmDeleteTreatmentId = null;
    }

    public function deleteTreatment(int $id): void
    {
        Treatment::where('id', $id)
            ->where('patient_id', $this->patientId)
            ->delete();

        $this->confirmDeleteTreatmentId = null;
        $this->refreshData();
        $this->loadToothTreatments();
    }

    // ─── Treatment Payments ────────────────────────────────────────

    public function openPaymentModal(int $treatmentId): void
    {
        $this->payingTreatmentId = $treatmentId;
        $this->showPaymentModal  = true;
        $this->paymentForm       = [
            'amount'         => '',
            'payment_method' => 'espèces',
            'paid_at'        => now()->toDateString(),
            'notes'          => '',
        ];
    }

    public function closePaymentModal(): void
    {
        $this->showPaymentModal  = false;
        $this->payingTreatmentId = null;
        $this->resetValidation();
    }

    public function savePayment(): void
    {
        $this->validate([
            'paymentForm.amount'         => 'required|numeric|min:0.001',
            'paymentForm.payment_method' => 'required|string',
            'paymentForm.paid_at'        => 'required|date',
        ]);

        $treatment = Treatment::where('id', $this->payingTreatmentId)
            ->where('patient_id', $this->patientId)
            ->firstOrFail();

        Payment::create([
            'patient_id'   => $this->patientId,
            'payable_id'   => $treatment->id,
            'payable_type' => Treatment::class,
            'amount'       => $this->paymentForm['amount'],
            'payment_method' => $this->paymentForm['payment_method'],
            'paid_at'      => $this->paymentForm['paid_at'],
            'notes'        => $this->paymentForm['notes'] ?: null,
        ]);

        $this->showPaymentModal  = false;
        $this->payingTreatmentId = null;
        $this->refreshData();
        $this->loadToothTreatments();
    }

    public function deletePayment(int $paymentId): void
    {
        Payment::where('id', $paymentId)
            ->where('patient_id', $this->patientId)
            ->delete();

        $this->refreshData();
        $this->loadToothTreatments();
    }

    // ─── Data ──────────────────────────────────────────────────────

    protected function refreshData(): void
    {
        $this->allTreatments = Treatment::where('patient_id', $this->patientId)
            ->whereNull('treatment_plan_id') // only standalone in the summary
            ->with('payments')
            ->orderByDesc('date')
            ->get();

        // Include ALL treatments (standalone + plan items) for tooth coloring
        $this->treatedTeeth = Treatment::where('patient_id', $this->patientId)
            ->whereNotNull('tooth_number')
            // ↓ No whereNull('treatment_plan_id') filter here
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
