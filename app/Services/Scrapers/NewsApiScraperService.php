<?php

namespace App\Services\Scrapers;

use andreskrey\Readability\Configuration;
use andreskrey\Readability\Readability;
use App\Enums\ApiProviderEnum;
use App\Models\ApiProvider;
use App\Models\Article;
use App\Models\Source;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NewsApiScraperService extends BaseScraperService {
    public function __construct() {
        parent::__construct(
            'https://newsapi.org/v2/top-headlines', [
                                                      'apiKey' => config('news.news_api_org_api_token'),
                                                  ]
        );
    }

    public function scrape(string $fromDate, string $toDate, string $category = null): void {
        $page         = 1;
        $perPage      = 10;  // Maximum per page for the free plan
        $totalResults = 1;   // Initialize with a number larger than 0 to enter the loop

        $apiProvider = ApiProvider::where('name', ApiProviderEnum::NEWS_API_ORG_PROVIDER->label())->first();
        Log::info('Fetching news from News API', ['category' => $category, 'fromDate' => $fromDate, 'toDate' => $toDate]);

        $categoryCollection = $this->getOrCreateCategory($category);

        // Fetch news from News API until we retrieve all articles
        while ($totalResults > ($page - 1) * $perPage) {
            Log::info("Fetching page $page of articles.");

            $response = $this->makeRequest(
                'https://newsapi.org/v2/top-headlines', [
                                                          'category' => $category,
                                                          'from'     => $fromDate,
                                                          'to'       => $toDate,
                                                          'searchIn' => 'title,description,content',
                                                          'language' => 'en',
                                                          'sortBy'   => 'publishedAt',
                                                          'apiKey'   => config('news.news_api_org_api_token'),
                                                          'page'     => $page,
                                                          'pageSize' => $perPage,
                                                      ]
            );
            // Check if response has articles
            if (isset($response['articles']) && is_array($response['articles'])) {
                DB::beginTransaction();

                try {
                    foreach ($response['articles'] as $articleData) {
                        // Store or retrieve the source
                        $source = Source::firstOrCreate(
                            ['name' => $articleData['source']['name']],
                            ['url' => $articleData['url'], 'api_provider_id' => $apiProvider->id]
                        );

                        $authors = $this->getOrCreateAuthors($articleData['author'] ?? null);

                        // Handle the published date
                        $publishedAt = Carbon::parse($articleData['publishedAt'])->toDateTimeString();

                        // Get the article content
                        $content = $this->getArticleContent($articleData['url']);

                        // Prepare article data for upsert
                        $article = [
                            'title'          => $articleData['title'],
                            'content'        => $content ?? '-',
                            'url'            => $articleData['url'],
                            'featured_image' => $articleData['urlToImage'] ?? null,
                            'published_at'   => $publishedAt,
                            'source_id'      => $source->id,
                            'category_id'    => $categoryCollection->id,
                            'author_id'      => count($authors) > 0 ? $authors[0]?->id : null,
                        ];

                        $article['slug'] = Str::slug($article['title']);

                        Article::upsert(
                            [$article],
                            ['slug'], // Unique constraint
                            ['title', 'stand_first', 'trail_text', 'main', 'content', 'url', 'featured_image', 'published_at', 'source_id', 'category_id', 'author_id', 'updated_at']
                        );

                        Log::info('Stored article', ['title' => $articleData['title'], 'url' => $articleData['url']]);
                    }

                    DB::commit(); // Commit the transaction
                    Log::info('Transaction committed successfully for page ' . $page);
                } catch (\Exception $e) {
                    DB::rollBack(); // Rollback in case of error
                    Log::error('Error storing articles on page ' . $page, ['error' => $e->getMessage()]);
                    dd('Error storing articles: ' . $e->getMessage());
                }
            }

            // Check the total number of results and move to the next page
            $totalResults = $response['totalResults'] ?? 0;
            Log::info("Total results fetched: $totalResults, moving to the next page.");
            $page++;
        }

        Log::info('All articles fetched and stored successfully.');
    }

    protected function getArticleContent($url): ?string {
        // Make an HTTP request to the article URL using Guzzle
        $client = new Client();

        try {
            $response = $client->get(
                $url, [
                        'timeout' => 30, // Set timeout to 30 seconds
                    ]
            );

            $html = (string)$response->getBody();  // Get the HTML content of the article

            if ($html) {
                // Try parsing and extracting the article content
                try {
                    // Use php-readability to extract the article content
                    $readability = new Readability(new Configuration());
                    $readability->parse($html);

                    // Get the cleaned article content
                    return $readability->getContent();
                } catch (\Error $e) {
                    // Log other parsing errors
                    Log::error("Error parsing article content", ['url' => $url, 'error' => $e->getMessage()]);
                }
            }
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // Handle request errors (timeouts, bad response, etc.)
            Log::error("HTTP Request Error", ['url' => $url, 'error' => $e->getMessage()]);
        } catch (\Exception $e) {
            // Log any other general errors
            Log::error("Error retrieving article content", ['url' => $url, 'error' => $e->getMessage()]);
        }

        return null; // Return null in case of failure
    }
}
