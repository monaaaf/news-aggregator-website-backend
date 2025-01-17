<?php

namespace App\Console\Commands;

use App\Services\Scrapers\NewsApiScraperService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ScrapeAllArticles extends Command {

    protected $signature = 'articles:scrape-all';

    protected $description = 'Scrape articles sequentially from multiple sources';

    public function handle(): void {
        // Run the Guardian scrape command
        $this->call('articles:scrape-guardian-articles');
        $this->info('Guardian articles scraped successfully.');

        // Run the News API scrape command
        $this->call('articles:scrape-news-api-articles');
        $this->info('News API articles scraped successfully.');

        // Run the NY Times scrape command
        $this->call('articles:scrape-n-y-times-articles');
        $this->info('NY Times articles scraped successfully.');
    }
}
