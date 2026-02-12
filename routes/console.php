<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule booking reminders (check-in and check-out) - runs daily at 9 AM
Schedule::command('bookings:send-reminders')
    ->dailyAt('09:00')
    ->timezone('Africa/Dar_es_Salaam')
    ->description('Send check-in and check-out reminder emails');

// Schedule expiration warnings - runs every 15 minutes
Schedule::command('bookings:send-expiration-warnings')
    ->everyFifteenMinutes()
    ->timezone('Africa/Nairobi')
    ->description('Send expiration warning emails (24h, 12h, 1h, and 15 minutes before expiration)');

// Schedule expired bookings check - runs every 5 minutes
Schedule::command('bookings:check-expired')
    ->everyFiveMinutes()
    ->timezone('Africa/Nairobi')
    ->description('Check and cancel bookings that have expired');

// Schedule feedback request emails - runs daily at 10 AM
Schedule::command('bookings:send-feedback-requests')
    ->dailyAt('10:00')
    ->timezone('Africa/Dar_es_Salaam')
    ->description('Send feedback request emails to guests 1-2 days after check-out');
