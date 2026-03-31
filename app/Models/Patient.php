<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $guarded = [];
    protected $fillable = [
        'cin',
        'first_name',
        'last_name',
        'birthdate',
        'gender',
        'phone',
        'email',
        'medical_history',
        'remarks',
        'created_at',
        'num_record',   // ← add this
    ];

    public $timestamps = false;

    protected static function booted(): void
    {
        static::creating(function (Patient $patient) {
            if (empty($patient->created_at)) {
                $patient->created_at = now();
            }
            $patient->updated_at = now();
        });
    }


    public function treatments()
    {
        return $this->hasMany(Treatment::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
