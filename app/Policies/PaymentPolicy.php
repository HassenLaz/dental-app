<?php

// ═══════════════════════════════════════════════════════
// app/Policies/PaymentPolicy.php
// ═══════════════════════════════════════════════════════

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool { return $user->isDentist(); }
    public function view(User $user, Payment $record): bool { return $user->isDentist(); }
    public function create(User $user): bool  { return $user->isDentist(); }
    public function update(User $user, Payment $record): bool { return $user->isDentist(); }
    public function delete(User $user, Payment $record): bool { return $user->isDentist(); }
    public function deleteAny(User $user): bool { return $user->isDentist(); }
}


// ═══════════════════════════════════════════════════════
// app/Policies/TreatmentPlanPolicy.php
// ═══════════════════════════════════════════════════════

namespace App\Policies;

use App\Models\TreatmentPlan;
use App\Models\User;

class TreatmentPlanPolicy
{
    public function viewAny(User $user): bool { return $user->isDentist(); }
    public function view(User $user, TreatmentPlan $record): bool { return $user->isDentist(); }
    public function create(User $user): bool  { return $user->isDentist(); }
    public function update(User $user, TreatmentPlan $record): bool { return $user->isDentist(); }
    public function delete(User $user, TreatmentPlan $record): bool { return $user->isDentist(); }
    public function deleteAny(User $user): bool { return $user->isDentist(); }
}