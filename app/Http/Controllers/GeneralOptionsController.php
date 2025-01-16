<?php

namespace App\Http\Controllers;

use App\Http\Resources\Misc\AuthorResource;
use App\Http\Resources\Misc\CategoryResource;
use App\Http\Resources\Misc\SourceResource;
use App\Models\Author;
use App\Models\Category;
use App\Models\Source;

class GeneralOptionsController extends Controller {
    public function options(): array {
        return [
            'data' => [
                'categories' => CategoryResource::collection(Category::all())->resolve(),
                'sources'    => SourceResource::collection(Source::all())->resolve(),
                'authors'    => AuthorResource::collection(Author::all())->resolve(),
            ]
        ];
    }
}
