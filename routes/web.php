<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleCalendarController;

Route::get('/google/redirect', [GoogleCalendarController::class, 'redirect'])
    ->name('google.redirect');
Route::get('/google/callback', [GoogleCalendarController::class, 'callback'])
    ->name('google.callback');

    
// Route::get('/', function () {
//     return view('welcome');
// });