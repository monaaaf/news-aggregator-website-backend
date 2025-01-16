<?php

namespace App\Services;

use App\Http\Requests\UserPreferenceRequest;
use App\Models\Article;
use App\Models\User;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ArticleService {
    public function query(): QueryBuilder {
        return QueryBuilder::for(Article::class)
                           ->defaultSort('-updated_at')
                           ->allowedIncludes(['category', 'source', 'author'])
                           ->allowedSorts(['id', 'title', 'updated_at'])
                           ->allowedFilters(
                               [
                                   'title',
                                   AllowedFilter::exact('category_ids', 'category.id'),
                                   AllowedFilter::exact('source_ids', 'source.id'),
                                   AllowedFilter::exact('author_ids', 'author.id'),
                                   AllowedFilter::scope('publish_date_range'),
                               ]
                           );
    }
}
