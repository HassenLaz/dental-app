<?php

// ═══════════════════════════════════════════════════════
// app/Policies/TreatmentPolicy.php
// ═══════════════════════════════════════════════════════

namespace App\Policies;

use App\Models\Treatment;
use App\Models\User;

class TreatmentPolicy
{
    public function viewAny(User $user): bool  { return $user->isDentist(); }
    public function view(User $user, Treatment $record): bool { return $user->isDentist(); }
    public function create(User $user): bool   { return $user->isDentist(); }
    public function update(User $user, Treatment $record): bool { return $user->isDentist(); }
    public function delete(User $user, Treatment $record): bool { return $user->isDentist(); }
    public function deleteAny(User $user): bool { return $user->isDentist(); }
}