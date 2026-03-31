<?php

// app/Models/TreatmentPlan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class TreatmentPlan extends Model
{
    protected $fillable = [
        'patient_id',
        'title',
        'notes',
        'total_cost',
        'amount_paid',
        'payment_status',
        'status',
        'created_date',
    ];

    protected $casts = [
        'created_date' => 'date',
        'total_cost'   => 'decimal:3',
        'amount_paid'  => 'decimal:3',
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

    // Items are now Treatment records with treatment_plan_id set
    public function items(): HasMany
    {
        return $this->hasMany(Treatment::class, 'treatment_plan_id')->orderBy('id');
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function syncPaymentStatus(): void
    {
        $paid = (float) $this->payments()->sum('amount');
        $this->amount_paid = $paid;

        $cost = (float) $this->total_cost;

        if ($paid <= 0)        $this->payment_status = 'pending';
        elseif ($paid < $cost) $this->payment_status = 'partial';
        else                   $this->payment_status = 'paid';

        $this->saveQuietly();
    }

    public function remainingBalance(): float
    {
        return max(0, (float) $this->total_cost - (float) $this->amount_paid);
    }

    public function completedItemsCount(): int
    {
        return $this->items()->where('status', 'completed')->count();
    }
}
