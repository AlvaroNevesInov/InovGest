<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule subscription management commands
Schedule::command('subscriptions:check-trials')->daily();
Schedule::command('subscriptions:convert-trials')->daily();
Schedule::command('subscriptions:process-renewals')->daily();
