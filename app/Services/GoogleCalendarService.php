<?php

// app/Services/GoogleCalendarService.php

namespace App\Services;

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;
use App\Models\Appointment;
use Illuminate\Support\Facades\Storage;

class GoogleCalendarService
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri(config('services.google.redirect'));
        $this->client->addScope(Calendar::CALENDAR);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');

        // Fix SSL on Windows/XAMPP
        $this->client->setHttpClient(
            new \GuzzleHttp\Client([
                'verify' => 'C:/xampp/php/cacert.pem',
            ])
        );

        // Load stored token if exists
        if (Storage::exists('google_token.json')) {
            $token = json_decode(Storage::get('google_token.json'), true);
            $this->client->setAccessToken($token);

            // Refresh if expired
            if ($this->client->isAccessTokenExpired()) {
                $refreshToken = $this->client->getRefreshToken();
                if ($refreshToken) {
                    $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
                    Storage::put('google_token.json', json_encode($this->client->getAccessToken()));
                }
            }
        }
    }

    public function isAuthenticated(): bool
    {
        return Storage::exists('google_token.json') && !$this->client->isAccessTokenExpired();
    }

    public function getAuthUrl(): string
    {
        return $this->client->createAuthUrl();
    }

    public function handleCallback(string $code): void
    {
        $token = $this->client->fetchAccessTokenWithAuthCode($code);
        $this->client->setAccessToken($token);
        Storage::put('google_token.json', json_encode($token));
    }

    public function createEvent(Appointment $appointment): ?string
    {
        if (!$this->isAuthenticated()) return null;

        $service = new Calendar($this->client);

        $event = new Event([
            'summary'     => $appointment->patient->first_name . ' ' . $appointment->patient->last_name,
            'description' => $appointment->notes ?? '',
            'start'       => new EventDateTime([
                'dateTime' => \Carbon\Carbon::parse($appointment->start_time)->toRfc3339String(),
                'timeZone' => 'Africa/Tunis',
            ]),
            'end'         => new EventDateTime([
                'dateTime' => \Carbon\Carbon::parse($appointment->end_time)->toRfc3339String(),
                'timeZone' => 'Africa/Tunis',
            ]),
            'reminders'   => [
                'useDefault' => false,
                'overrides'  => [
                    ['method' => 'popup', 'minutes' => 30],
                ],
            ],
        ]);

        $createdEvent = $service->events->insert(
            config('services.google.calendar_id', 'primary'),
            $event
        );

        return $createdEvent->getId(); // store this to update/delete later
    }

    public function updateEvent(string $googleEventId, Appointment $appointment): void
    {
        if (!$this->isAuthenticated()) return;

        $service  = new Calendar($this->client);
        $calId    = config('services.google.calendar_id', 'primary');
        $event    = $service->events->get($calId, $googleEventId);

        $event->setSummary($appointment->patient->first_name . ' ' . $appointment->patient->last_name);
        $event->setDescription($appointment->notes ?? '');
        $event->setStart(new EventDateTime([
            'dateTime' => \Carbon\Carbon::parse($appointment->start_time)->toRfc3339String(),
            'timeZone' => 'Africa/Tunis',
        ]));
        $event->setEnd(new EventDateTime([
            'dateTime' => \Carbon\Carbon::parse($appointment->end_time)->toRfc3339String(),
            'timeZone' => 'Africa/Tunis',
        ]));

        $service->events->update($calId, $googleEventId, $event);
    }

    public function deleteEvent(string $googleEventId): void
    {
        if (!$this->isAuthenticated()) return;

        $service = new Calendar($this->client);
        $service->events->delete(
            config('services.google.calendar_id', 'primary'),
            $googleEventId
        );
    }
}
