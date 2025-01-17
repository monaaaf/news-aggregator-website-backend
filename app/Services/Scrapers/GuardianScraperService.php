<?php

namespace App\Services\Scrapers;

use App\Enums\ApiProviderEnum;
use App\Models\ApiProvider;
use App\Models\Article;
use App\Models\Source;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GuardianScraperService extends BaseScraperService {

    public function __construct() {
        parent::__construct(
            'https://content.guardianapis.com',
            [
                'api-key' => config('news.the_guardian_api_token'),
            ]
        );
    }

    public function scrape(string $fromDate, string $toDate, string $category = null): void {
        $page    = 1;
        $perPage = 50; // Number of articles per page

        $apiProvider = ApiProvider::where('name', ApiProviderEnum::THE_GUARDIAN_PROVIDER->label())->firstOrFail();

        do {
            Log::info("Fetching page $page of articles from The Guardian.");

            $response = $this->makeRequest(
                '/search',
                [
                    'format'       => 'json',
                    'from-date'    => $fromDate,
                    'to-date'      => $toDate,
                    'order-by'     => 'newest',
                    'show-fields'  => 'all',
                    'show-tags'    => 'all',
                    'show-blocks'  => 'all',
                    'show-section' => 'true',
                    'page'         => $page,
                    'page-size'    => $perPage,
                ]
            );

            $articles = $response['response']['results'] ?? [];
            if (empty($articles)) {
                break;
            }

            DB::beginTransaction();

            try {
                foreach ($articles as $articleData) {
                    $this->processArticle($articleData, $apiProvider);
                }

                DB::commit();
                Log::info("Successfully processed page $page.");
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error(
                    "Error processing page $page: " . $e->getMessage(), [
                                                                          'trace' => $e->getTraceAsString(),
                                                                      ]
                );
                throw $e;
            }

            $page++;
        } while ($response['response']['pages'] >= $page);

        Log::info('All articles fetched and stored successfully from The Guardian.');
    }

    private function processArticle(array $articleData, ApiProvider $apiProvider): void {
        $source = Source::firstOrCreate(
            ['name' => 'The Guardian'],
            ['url' => $articleData['webUrl'], 'api_provider_id' => $apiProvider->id]
        );

        $authors     = $this->getOrCreateAuthors($articleData['fields']['byline'] ?? null);
        $category    = $this->getOrCreateCategory($articleData['sectionName']);
        $publishedAt = Carbon::parse($articleData['webPublicationDate'])->toDateTimeString();

        $article = [
            'title'          => $articleData['webTitle'],
            'stand_first'    => $articleData['fields']['standfirst'] ?? '',
            'trail_text'     => $articleData['fields']['trailText'] ?? '',
            'main'           => $articleData['fields']['main'] ?? '',
            'content'        => $articleData['fields']['body'] ?? '',
            'url'            => $articleData['webUrl'],
            'featured_image' => $articleData['fields']['thumbnail'] ?? null,
            'published_at'   => $publishedAt,
            'source_id'      => $source->id,
            'category_id'    => $category->id,
            'author_id'      => count($authors) > 0 ? $authors[0]?->id : null,
        ];

        $article['slug'] = Str::slug($article['title']);

        Article::upsert(
            [$article],
            ['slug'], // Unique constraint
            ['title', 'stand_first', 'trail_text', 'main', 'content', 'url', 'featured_image', 'published_at', 'source_id', 'category_id', 'author_id', 'updated_at']
        );

        Log::info('Stored article', ['title' => $article['title'], 'url' => $article['url']]);
    }
}
