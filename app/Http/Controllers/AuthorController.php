<?php

namespace App\Http\Controllers;

use App\Http\Resources\Misc\AuthorResource;
use App\Services\AuthorService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AuthorController extends Controller {
    private AuthorService $authorService;

    public function __construct() {
        $this->authorService = new AuthorService();
    }

    public function authors(): AnonymousResourceCollection {
        return AuthorResource::collection($this->authorService->query()->paginate());
    }

    public function topAuthors(): AnonymousResourceCollection {
        $topAuthors = $this->authorService->query()
                                          ->withCount('articles')
                                          ->orderBy('articles_count', 'desc')
                                          ->take(6)
                                          ->get();

        return AuthorResource::collection($topAuthors);
    }
}
