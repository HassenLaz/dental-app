<?php

// app/Livewire/TreatmentPlanManager.php

namespace App\Livewire;

use App\Models\Payment;
use App\Models\Treatment;
use App\Models\TreatmentPlan;
use Livewire\Component;
use Illuminate\Support\Collection;

class TreatmentPlanManager extends Component
{
    public int $patientId;
    public Collection $plans;

    // Create form
    public bool $showPlanForm = false;
    public array $planForm = [
        'title'        => 'Plan de traitement',
        'notes'        => '',
        'total_cost'   => '',
        'created_date' => '',
    ];
    public array $planItems = [
        ['tooth_number' => '', 'name' => '', 'description' => ''],
    ];

    // Edit form
    public bool  $showEditForm  = false;
    public ?int  $editingPlanId = null;
    public array $editForm      = [
        'title'        => '',
        'notes'        => '',
        'total_cost'   => '',
        'created_date' => '',
        'status'       => '',
    ];

    // Delete confirmation
    public ?int $confirmDeletePlanId = null;

    // Autocomplete
    public array $suggestions      = [];
    public bool  $showSuggestions  = false;
    public ?int  $focusedItemIndex = null;
    protected array $dentalActs    = [];

    // Payment form
    public ?int  $payingPlanId    = null;
    public bool  $showPaymentForm = false;
    public array $paymentForm     = [
        'amount'         => '',
        'payment_method' => 'espèces',
        'paid_at'        => '',
        'notes'          => '',
    ];

    // Expanded plan
    public ?int $expandedPlanId = null;

    public function mount(int $patientId): void
    {
        $this->patientId               = $patientId;
        $this->planForm['created_date']  = now()->toDateString();
        $this->paymentForm['paid_at']    = now()->toDateString();
        $this->loadDentalActs();
        $this->loadPlans();
    }

    protected function loadDentalActs(): void
    {
        $path = storage_path('app/dental-acts.json');
        if (file_exists($path)) {
            $this->dentalActs = json_decode(file_get_contents($path), true) ?? [];
        }
    }

    public function loadPlans(): void
    {
        $this->plans = TreatmentPlan::where('patient_id', $this->patientId)
            ->with(['items', 'payments'])
            ->orderByDesc('created_date')
            ->get();
    }

    // ─── Plan Items ────────────────────────────────────────────────

    public function addItem(): void
    {
        $this->planItems[] = ['tooth_number' => '', 'name' => '', 'description' => ''];
    }

    public function removeItem(int $index): void
    {
        array_splice($this->planItems, $index, 1);
        if (empty($this->planItems)) {
            $this->planItems = [['tooth_number' => '', 'name' => '', 'description' => '']];
        }
    }

    // ─── Autocomplete ──────────────────────────────────────────────

    public function updatedPlanItems($value, $key): void
    {
        if (!str_ends_with((string) $key, '.name')) return;

        $index = (int) explode('.', (string) $key)[0];
        $this->focusedItemIndex = $index;
        $value = trim((string) $value);

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

    public function selectItemSuggestion(string $name, int $index): void
    {
        $this->planItems[$index]['name'] = $name;
        $this->suggestions              = [];
        $this->showSuggestions          = false;
        $this->focusedItemIndex         = null;
    }

    public function hideSuggestions(): void
    {
        $this->showSuggestions  = false;
        $this->suggestions      = [];
        $this->focusedItemIndex = null;
    }

    // ─── Save Plan ─────────────────────────────────────────────────

    public function savePlan(): void
    {
        $this->validate([
            'planForm.title'        => 'required|string|max:255',
            'planForm.total_cost'   => 'required|numeric|min:0',
            'planForm.created_date' => 'required|date',
            'planItems.*.name'      => 'required|string|max:255',
        ], [
            'planItems.*.name.required' => 'Chaque acte doit avoir un nom.',
        ]);

        $plan = TreatmentPlan::create([
            'patient_id'     => $this->patientId,
            'title'          => $this->planForm['title'],
            'notes'          => $this->planForm['notes'] ?: null,
            'total_cost'     => $this->planForm['total_cost'],
            'amount_paid'    => 0,
            'payment_status' => 'pending',
            'status'         => 'draft',
            'created_date'   => $this->planForm['created_date'],
        ]);

        // Save items as Treatment records with status=planned
        foreach ($this->planItems as $item) {
            if (empty(trim($item['name']))) continue;
            Treatment::create([
                'patient_id'        => $this->patientId,
                'treatment_plan_id' => $plan->id,
                'tooth_number'      => $item['tooth_number'] ?: null,
                'name'              => $item['name'],
                'description'       => $item['description'] ?: null,
                'status'            => 'planned',
                'cost'              => null,  // individual cost managed at plan level
                'date'              => now()->toDateString(),
            ]);
        }

        $this->showPlanForm = false;
        $this->planForm     = [
            'title'        => 'Plan de traitement',
            'notes'        => '',
            'total_cost'   => '',
            'created_date' => now()->toDateString(),
        ];
        $this->planItems = [['tooth_number' => '', 'name' => '', 'description' => '']];
        $this->loadPlans();
    }

    // ─── Edit Plan ─────────────────────────────────────────────────

    public function openEditForm(int $planId): void
    {
        $plan = TreatmentPlan::where('patient_id', $this->patientId)->findOrFail($planId);
        $this->editingPlanId = $planId;
        $this->editForm      = [
            'title'        => $plan->title,
            'notes'        => $plan->notes ?? '',
            'total_cost'   => $plan->total_cost,
            'created_date' => $plan->created_date->format('Y-m-d'),
            'status'       => $plan->status,
        ];
        $this->showEditForm    = true;
        $this->showPaymentForm = false;
    }

    public function saveEdit(): void
    {
        $this->validate([
            'editForm.title'        => 'required|string|max:255',
            'editForm.total_cost'   => 'required|numeric|min:0',
            'editForm.created_date' => 'required|date',
            'editForm.status'       => 'required|in:draft,approved,in_progress,completed',
        ]);

        $plan = TreatmentPlan::where('patient_id', $this->patientId)->findOrFail($this->editingPlanId);
        $plan->update([
            'title'        => $this->editForm['title'],
            'notes'        => $this->editForm['notes'] ?: null,
            'total_cost'   => $this->editForm['total_cost'],
            'created_date' => $this->editForm['created_date'],
            'status'       => $this->editForm['status'],
        ]);
        $plan->syncPaymentStatus();

        $this->showEditForm  = false;
        $this->editingPlanId = null;
        $this->loadPlans();
    }

    public function cancelEdit(): void
    {
        $this->showEditForm  = false;
        $this->editingPlanId = null;
        $this->resetValidation();
    }

    // ─── Delete Plan ───────────────────────────────────────────────

    public function confirmDeletePlan(int $planId): void
    {
        $this->confirmDeletePlanId = $planId;
    }

    public function cancelDeletePlan(): void
    {
        $this->confirmDeletePlanId = null;
    }

    public function deletePlan(int $planId): void
    {
        TreatmentPlan::where('id', $planId)
            ->where('patient_id', $this->patientId)
            ->delete();
        // Treatment items with this plan_id get treatment_plan_id = null (nullOnDelete)
        $this->confirmDeletePlanId = null;
        if ($this->expandedPlanId === $planId) $this->expandedPlanId = null;
        $this->loadPlans();
    }

    // ─── Item Status ───────────────────────────────────────────────

    public function updatePlanStatus(int $planId, string $status): void
    {
        TreatmentPlan::where('id', $planId)->where('patient_id', $this->patientId)
            ->update(['status' => $status]);
        $this->loadPlans();
    }

    public function updateItemStatus(int $itemId, string $status): void
    {
        Treatment::whereHas('plan', fn($q) => $q->where('patient_id', $this->patientId))
            ->where('id', $itemId)
            ->update(['status' => $status]);
        $this->loadPlans();
    }

    // ─── Payments ──────────────────────────────────────────────────

    public function openPaymentForm(int $planId): void
    {
        $this->payingPlanId    = $planId;
        $this->showPaymentForm = true;
        $this->showEditForm    = false;
        $this->paymentForm     = [
            'amount'         => '',
            'payment_method' => 'espèces',
            'paid_at'        => now()->toDateString(),
            'notes'          => '',
        ];
    }

    public function savePayment(): void
    {
        $this->validate([
            'paymentForm.amount'         => 'required|numeric|min:0.001',
            'paymentForm.payment_method' => 'required|string',
            'paymentForm.paid_at'        => 'required|date',
        ]);

        $plan = TreatmentPlan::where('id', $this->payingPlanId)
            ->where('patient_id', $this->patientId)
            ->firstOrFail();

        Payment::create([
            'patient_id'     => $this->patientId,
            'payable_id'     => $plan->id,
            'payable_type'   => TreatmentPlan::class,
            'amount'         => $this->paymentForm['amount'],
            'payment_method' => $this->paymentForm['payment_method'],
            'paid_at'        => $this->paymentForm['paid_at'],
            'notes'          => $this->paymentForm['notes'] ?: null,
        ]);

        $this->showPaymentForm = false;
        $this->payingPlanId    = null;
        $this->loadPlans();
    }

    public function deletePayment(int $paymentId): void
    {
        Payment::where('id', $paymentId)->where('patient_id', $this->patientId)->delete();
        $this->loadPlans();
    }

    public function toggleExpand(int $planId): void
    {
        $this->expandedPlanId = $this->expandedPlanId === $planId ? null : $planId;
    }

    public function render()
    {
        return view('livewire.treatment-plan-manager');
    }
}
