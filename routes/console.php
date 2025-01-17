<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command(
    'inspire', function () {
    $this->comment(Inspiring::quote());
}
)->purpose('Display an inspiring quote')->hourly();

info('Hello world!');

Schedule::command('app:test-job')->everyMinute();

Schedule::command('articles:scrape-all')->everyFourHours();
