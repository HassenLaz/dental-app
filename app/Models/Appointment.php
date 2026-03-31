<?php

namespace App\Models;

use App\Services\GoogleCalendarService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class Appointment extends Model
{
    protected $fillable = [
        'patient_id',
        'start_time',
        'end_time',
        'status',
        'notes',
        'google_event_id',  // ← make sure this is in fillable
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
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

    /*
    |--------------------------------------------------------------------------
    | Google Calendar Sync
    |--------------------------------------------------------------------------
    */

    protected static function booted(): void
    {
        static::created(function (Appointment $appointment) {
            try {
                $appointment->load('patient');
                $google  = app(GoogleCalendarService::class);
                $eventId = $google->createEvent($appointment);
                if ($eventId) {
                    $appointment->updateQuietly(['google_event_id' => $eventId]);
                }
            } catch (\Exception $e) {
                Log::warning('Google Calendar create failed: ' . $e->getMessage());
            }
        });

        static::updated(function (Appointment $appointment) {
            try {
                $appointment->load('patient');
                $google = app(GoogleCalendarService::class);
                if ($appointment->google_event_id) {
                    $google->updateEvent($appointment->google_event_id, $appointment);
                }
            } catch (\Exception $e) {
                Log::warning('Google Calendar update failed: ' . $e->getMessage());
            }
        });

        static::deleted(function (Appointment $appointment) {
            try {
                $google = app(GoogleCalendarService::class);
                if ($appointment->google_event_id) {
                    $google->deleteEvent($appointment->google_event_id);
                }
            } catch (\Exception $e) {
                Log::warning('Google Calendar delete failed: ' . $e->getMessage());
            }
        });
    }
}
