<?php

namespace Database\Seeders;

use App\Enums\ApiProviderEnum;
use App\Models\ApiProvider;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        ApiProvider::insert(
            [
                ['name' => ApiProviderEnum::NEWS_API_ORG_PROVIDER->label(), 'url' => 'https://newsapi.org'],
                ['name' => ApiProviderEnum::THE_GUARDIAN_PROVIDER->label(), 'url' => 'https://www.theguardian.com'],
                ['name' => ApiProviderEnum::NEW_YORK_TIMES_PROVIDER->label(), 'url' => 'https://www.nytimes.com'],
            ]
        );
    }
}
