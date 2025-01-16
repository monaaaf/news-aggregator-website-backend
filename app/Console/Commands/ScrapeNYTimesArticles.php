<?php

namespace App\Console\Commands;

use App\Services\Scrapers\NYTimesScraperService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ScrapeNYTimesArticles extends Command {
    protected NYTimesScraperService $nYTimesScraperService;

    protected $signature = 'articles:scrape-n-y-times-articles';

    protected $description = 'Scrape articles from NYTimes and store them in the database';

    public function handle(): void {
        try {
            Log::info("Command: Scrape articles from NYTimes.");

            $endDate   = Carbon::now()->format('Y-m-d');
            $startDate = Carbon::yesterday()->format('Y-m-d');

            $this->nYTimesScraperService = new NYTimesScraperService();
            $this->nYTimesScraperService->scrape($startDate, $endDate);

            Log::info("Command: Scrape articles from NYTimes completed successfully.");
        } catch (\Exception $e) {
            Log::error("Command: An error occurred during scraping articles from NYTimes: " . $e->getMessage());
        }
    }
}
