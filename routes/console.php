<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command(
    'inspire', function () {
    $this->comment(Inspiring::quote());
}
)->purpose('Display an inspiring quote')->hourly();

Schedule::command('articles:scrape-guardian-articles')->everyFourHours();
Schedule::command('articles:scrape-news-api-articles')->everyFourHours();
Schedule::command('articles:scrape-n-y-times-articles')->everyFourHours();
