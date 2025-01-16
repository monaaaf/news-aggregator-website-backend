<?php

namespace App\Services;

use App\Models\Category;
use Spatie\QueryBuilder\QueryBuilder;

class CategoryService {
    public function query(): QueryBuilder {
        return QueryBuilder::for(Category::class)
                           ->defaultSort('-updated_at')
                           ->allowedSorts(['id', 'name', 'updated_at'])
                           ->allowedFilters(
                               [
                                   'name'
                               ]
                           );
    }
}
