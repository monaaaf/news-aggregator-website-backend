<?php

namespace App\Services\Scrapers;

use andreskrey\Readability\Configuration;
use andreskrey\Readability\Readability;
use App\Models\Author;
use App\Models\Category;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

abstract class BaseScraperService {
    protected Client $client;

    public function __construct(string $baseUrl, array $headers = []) {
        $this->client = new Client(
            [
                'base_uri' => $baseUrl,
                'headers'  => $headers,
                'timeout'  => 30.0,
            ]
        );
    }

    protected function makeRequest(string $endpoint, array $query = []): array {
        try {
            $response = $this->client->get($endpoint, ['query' => $query]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            throw new \Exception("Request failed: {$e->getMessage()}");
        }
    }

    abstract public function scrape(string $fromDate, string $toDate, string $category = null): void;

    protected function getOrCreateCategory(?string $name): Category|null {
        if (!$name) {
            return null;
        }

        $slug = Str::slug($name);

        return Category::firstOrCreate(
            ['slug' => $slug],
            ['name' => $name]
        );
    }

    protected function getOrCreateAuthors(?string $name): array {
        if (!$name) {
            return [];
        }

        // Remove any leading 'By ' from the string
        $authorName = str_replace('By ', '', $name);

        // Check if the string contains a comma
        if (strpos($authorName, ',') !== false) {
            // Split the string by commas to handle multiple authors
            $authorNames = array_map('trim', explode(',', $authorName));

            // Create or find each author and return an array of Author models
            return array_map(
                function ($authorName) {
                    return Author::firstOrCreate(
                        ['name' => $authorName],
                        ['email' => null]
                    );
                }, $authorNames
            );
        }

        // If no comma, handle a single author
        return [
            Author::firstOrCreate(
                ['name' => trim($authorName)],
                ['email' => null]
            ),
        ];
    }

    protected function getArticleContent(string $url): ?string {
        $client = new Client();

        try {
            $response = $client->get(
                $url, [
                        'timeout' => 30,
                        'headers' => [
                            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                        ],
                    ]
            );

            $html = (string)$response->getBody();

            if ($html) {
                $readability = new Readability(new Configuration());
                $readability->parse($html);

                return $readability->getContent();
            }
        } catch (\Exception $e) {
            Log::error("Failed to fetch article content.", ['url' => $url, 'error' => $e->getMessage()]);
        }

        return null;
    }
}
