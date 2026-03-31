<?php

// app/Http/Controllers/GoogleCalendarController.php

namespace App\Http\Controllers;

use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;

class GoogleCalendarController extends Controller
{
    public function redirect(GoogleCalendarService $google)
    {
        return redirect($google->getAuthUrl());
    }

    public function callback(Request $request, GoogleCalendarService $google)
    {
        if ($request->has('code')) {
            $google->handleCallback($request->get('code'));
        }

        return redirect('/')->with('success', 'Google Calendar connecté avec succès !');
    }
}