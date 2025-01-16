<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum ApiProviderEnum: string {
    use EnumToArray;

    case NEWS_API_ORG_PROVIDER   = 'news_api_org';
    case THE_GUARDIAN_PROVIDER   = 'the_guardian';
    case NEW_YORK_TIMES_PROVIDER = 'new_york_times';

    public function label(): string {
        return self::getLabel($this);
    }

    public static function getLabel(self $value): string {
        return match ($value) {
            self::NEWS_API_ORG_PROVIDER   => 'NewsAPI',
            self::THE_GUARDIAN_PROVIDER   => 'The Guardian',
            self::NEW_YORK_TIMES_PROVIDER => 'The New York Times',
        };
    }
}
