<?php

namespace App\Http\Controllers;

use App\Services\Scrapers\GuardianScraperService;
use App\Services\Scrapers\NewsApiScraperService;
use App\Services\Scrapers\NYTimesScraperService;

class TestController {
    protected GuardianScraperService $guardianScraper;
    protected NYTimesScraperService  $nYTimesScraperService;
    protected NewsApiScraperService  $newsApiScraperService;

    public function test() {
//        (new TheGuardianService())->fetchNews('2025-01-13', '2025-01-14');
//        (new NewsApiOrgService())->fetchNews('business','2025-01-13', '2025-01-15');
//        (new NewYorkTimesService())->fetchNews('2025-01-13', '2025-01-14');

//        $this->guardianScraper = new GuardianScraperService();
//        $this->nYTimesScraperService = new NYTimesScraperService();
        $this->newsApiScraperService = new NewsApiScraperService();

//        $this->guardianScraper->scrape('2025-01-13', '2025-01-14');
//        $this->nYTimesScraperService->scrape('2025-01-13', '2025-01-14');
        $this->newsApiScraperService->scrape('2025-01-13', '2025-01-14', 'business');
    }
}
