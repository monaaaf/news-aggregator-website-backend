<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum NewsApiOrgCategoryEnum: string {
    use EnumToArray;

    case BUSINESS      = 'business';
    case ENTERTAINMENT = 'entertainment';
    case GENERAL       = 'general';
    case HEALTH        = 'health';
    case SCIENCE       = 'science';
    case SPORTS        = 'sports';
    case TECHNOLOGY    = 'technology';
}
