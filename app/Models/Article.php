<?php

namespace App\Models;

use App\Traits\Sluggable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model {
    protected $fillable
        = [
            'title',
            'stand_first',
            'trail_text',
            'main',
            'content',
            'url',
            'featured_image',
            'published_at',
            'source_id',
            'category_id',
            'author_id',
            'slug',
        ];

    public function source(): BelongsTo {
        return $this->belongsTo(Source::class);
    }

    public function category(): BelongsTo {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo {
        return $this->belongsTo(Author::class);
    }

    public function scopePublishDateRange(Builder $query, ...$dates): Builder {
        return $query->whereBetween('published_at', [Carbon::parse($dates[0]), Carbon::parse($dates[1])]);
    }
}
