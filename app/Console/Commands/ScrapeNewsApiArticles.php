<?php

namespace App\Console\Commands;

use App\Services\Scrapers\NewsApiScraperService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ScrapeNewsApiArticles extends Command {
    protected NewsApiScraperService $newsApiScraperService;

    protected $signature = 'articles:scrape-news-api-articles';

    protected $description = 'Scrape articles from News Api and store them in the database';

    public function handle(): void {
        try {
            Log::info("Command: Scrape articles from News Api.");

            /*
             * The category you want to get headlines for.
             * Possible options: business entertainment general health science sports technology.
             *
             * Reference: https://newsapi.org/docs/endpoints/top-headlines
             * */

            $endDate   = Carbon::now()->format('Y-m-d');
            $startDate = Carbon::yesterday()->format('Y-m-d');

            $this->newsApiScraperService = new NewsApiScraperService();

            Log::info("Command: Scrape articles from News Api for business category.");
            $this->newsApiScraperService->scrape($startDate, $endDate, 'business');
            Log::info("Command: Scrape articles from News Api for business category completed successfully.");

            Log::info("Command: Scrape articles from News Api for entertainment category.");
            $this->newsApiScraperService->scrape($startDate, $endDate, 'entertainment');
            Log::info("Command: Scrape articles from News Api for entertainment category completed successfully.");

            Log::info("Command: Scrape articles from News Api for general category.");
            $this->newsApiScraperService->scrape($startDate, $endDate, 'general');
            Log::info("Command: Scrape articles from News Api for general category completed successfully.");

            Log::info("Command: Scrape articles from News Api for health category.");
            $this->newsApiScraperService->scrape($startDate, $endDate, 'health');
            Log::info("Command: Scrape articles from News Api for health category completed successfully.");

            Log::info("Command: Scrape articles from News Api for science category.");
            $this->newsApiScraperService->scrape($startDate, $endDate, 'science');
            Log::info("Command: Scrape articles from News Api for science category completed successfully.");

            Log::info("Command: Scrape articles from News Api for sports category.");
            $this->newsApiScraperService->scrape($startDate, $endDate, 'sports');
            Log::info("Command: Scrape articles from News Api for sports category completed successfully.");

            Log::info("Command: Scrape articles from News Api for technology category.");
            $this->newsApiScraperService->scrape($startDate, $endDate, 'technology');
            Log::info("Command: Scrape articles from News Api for technology category completed successfully.");

            Log::info("Command: Scrape articles from News Api completed successfully.");
        } catch (\Exception $e) {
            Log::error("Command: An error occurred during scraping articles from News Api: " . $e->getMessage());
        }
    }
}
