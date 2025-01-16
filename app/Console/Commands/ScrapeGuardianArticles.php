<?php

namespace App\Console\Commands;

use App\Services\Scrapers\GuardianScraperService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ScrapeGuardianArticles extends Command {
    protected GuardianScraperService $guardianScraper;

    protected $signature = 'articles:scrape-guardian-articles';

    protected $description = 'Scrape articles from Guardian and store them in the database';

    public function handle(): void {
        try {
            Log::info("Command: Scrape articles from Guardian.");

            $endDate   = Carbon::now()->format('Y-m-d');
            $startDate = Carbon::yesterday()->format('Y-m-d');

            $this->guardianScraper = new GuardianScraperService();
            $this->guardianScraper->scrape($startDate, $endDate);

            Log::info("Command: Scrape articles from Guardian completed successfully.");
        } catch (\Exception $e) {
            Log::error("Command: An error occurred during scraping articles from Guardian: " . $e->getMessage());
        }
    }
}
