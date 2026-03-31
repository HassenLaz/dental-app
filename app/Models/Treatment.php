<?php

// app/Models/Treatment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Treatment extends Model
{
    protected $fillable = [
        'patient_id',
        'treatment_plan_id',
        'tooth_number',
        'name',
        'description',
        'cost',
        'status',   // standalone | planned | completed | cancelled
        'date',
    ];

    protected $casts = [
        'date' => 'date',
        'cost' => 'decimal:3',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(TreatmentPlan::class, 'treatment_plan_id');
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    /*
    |--------------------------------------------------------------------------
    | Computed helpers
    |--------------------------------------------------------------------------
    */

    public function totalPaid(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    public function remainingBalance(): float
    {
        return max(0, (float) $this->cost - $this->totalPaid());
    }

    public function paymentStatus(): string
    {
        $paid = $this->totalPaid();
        $cost = (float) $this->cost;

        if ($cost <= 0 || $paid >= $cost) return 'paid';
        if ($paid > 0)                    return 'partial';
        return 'pending';
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeStandalone($query)
    {
        return $query->whereNull('treatment_plan_id');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
