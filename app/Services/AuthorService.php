<?php

namespace App\Services;

use App\Models\Author;
use Spatie\QueryBuilder\QueryBuilder;

class AuthorService {
    public function query(): QueryBuilder {
        return QueryBuilder::for(Author::class)
                           ->defaultSort('-updated_at')
                           ->allowedSorts(['id', 'name', 'updated_at'])
                           ->allowedFilters(
                               [
                                   'name'
                               ]
                           );
    }
}
