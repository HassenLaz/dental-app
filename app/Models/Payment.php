<?php

// app/Models/Payment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payment extends Model
{
    protected $fillable = [
        'patient_id',
        'payable_id',
        'payable_type',
        'amount',
        'payment_method',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'paid_at' => 'date',
        'amount'  => 'decimal:3',
    ];

    // The treatment or plan this payment belongs to
    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    // After a payment is saved, sync the parent's amount_paid
    protected static function booted(): void
    {
        $sync = function (Payment $payment) {
            $payable = $payment->payable;
            if ($payable && method_exists($payable, 'syncPaymentStatus')) {
                $payable->syncPaymentStatus();
            }
        };

        static::created($sync);
        static::updated($sync);
        static::deleted($sync);
    }
}