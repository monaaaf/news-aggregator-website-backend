<?php

namespace App\Services\Scrapers;

use andreskrey\Readability\Configuration;
use andreskrey\Readability\Readability;
use App\Enums\ApiProviderEnum;
use App\Models\ApiProvider;
use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NYTimesScraperService extends BaseScraperService {
    private const PER_PAGE = 10; // Default articles per page
    private const URL      = 'https://api.nytimes.com/svc/search/v2/articlesearch.json';

    public function __construct() {
        parent::__construct(
            self::URL,
            [
                'api-key' => config('news.new_york_times_api_token')
            ]
        );
    }

    public function scrape(string $fromDate, string $toDate, string $category = null): void {
        $apiProvider = ApiProvider::firstWhere('name', ApiProviderEnum::NEW_YORK_TIMES_PROVIDER->label());

        if (!$apiProvider) {
            Log::error('API provider not found for New York Times.');

            return;
        }

        $totalResults = $this->fetchTotalResults($fromDate, $toDate);

        if (!$totalResults) {
            Log::info('No articles found for the specified date range.');

            return;
        }

        $totalPages = (int)ceil($totalResults / self::PER_PAGE);

        Log::info("Starting article scrape. Total articles: $totalResults. Pages to fetch: $totalPages.");

        for ($page = 0; $page < $totalPages; $page++) {
            $this->processPage($page, $fromDate, $toDate, $apiProvider);
        }

        Log::info('Article scraping completed.');
    }

    private function fetchTotalResults(string $fromDate, string $toDate): ?int {
        $response = $this->makeRequest(
            self::URL, [
                         'begin_date' => $fromDate,
                         'end_date'   => $toDate,
                         'page'       => 0,
                         'api-key'    => config('news.new_york_times_api_token'),
                     ]
        );

        return $response['response']['meta']['hits'] ?? null;
    }

    private function processPage(int $page, string $fromDate, string $toDate, ApiProvider $apiProvider): void {
        Log::info("Fetching page $page.");

        $response = $this->makeRequest(
            self::URL, [
                         'begin_date' => $fromDate,
                         'end_date'   => $toDate,
                         'page'       => $page,
                         'api-key'    => config('news.new_york_times_api_token')
                     ]
        );

        if (!isset($response['response']['docs']) || !is_array($response['response']['docs'])) {
            Log::warning("No articles found on page $page.");

            return;
        }

        DB::beginTransaction();

        try {
            foreach ($response['response']['docs'] as $articleData) {
                $this->storeArticle($articleData, $apiProvider);
            }

            DB::commit();
            Log::info("Page $page processed successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error processing page $page: " . $e->getMessage());
        }
    }

    private function storeArticle(array $articleData, ApiProvider $apiProvider): void {
        $source = Source::firstOrCreate(
            ['name' => 'The New York Times'],
            ['url' => 'https://www.nytimes.com/', 'api_provider_id' => $apiProvider->id]
        );

        $authors  = $this->getOrCreateAuthors($articleData['byline']['original'] ?? null);
        $category = $this->getOrCreateCategory($articleData['section_name'] ?? null);

        $publishedAt = Carbon::parse($articleData['pub_date'])->toDateTimeString();
        $content     = $this->getArticleContent($articleData['web_url']);
        $imageUrl    = $this->extractImageUrl($articleData['multimedia'] ?? []);

        $article = [
            'title'          => $articleData['headline']['main'],
            'trail_text'     => $articleData['abstract'] ?? '',
            'content'        => $content ?? '-',
            'url'            => $articleData['web_url'],
            'featured_image' => $imageUrl,
            'published_at'   => $publishedAt,
            'source_id'      => $source->id,
            'category_id'    => $category?->id,
            'author_id'      => count($authors) > 0 ? $authors[0]?->id : null,
        ];

        Article::upsert(
            [$article],
            ['url'], // Unique constraint
            ['title', 'trail_text', 'content', 'featured_image', 'published_at', 'source_id', 'category_id', 'author_id', 'updated_at']
        );

        Log::info('Article stored.', ['title' => $article['title'], 'url' => $article['url']]);
    }

    private function extractImageUrl(array $multimedia): ?string {
        if (empty($multimedia)) {
            return null;
        }

        $image = $multimedia[0];

        return 'https://static01.nyt.com/' . $image['url'];
    }
}
