<?php

namespace App\Http\Controllers;

use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Services\ArticleService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller {
    private ArticleService $articleService;

    public function __construct() {
        $this->articleService = new ArticleService();
    }

    public function articles(Request $request): AnonymousResourceCollection {
        $query = $this->articleService->query();

        if (!$request->has('filter') && $user = Auth::user()) {
            $categories = $user->categories->pluck('id')->toArray();
            $sources    = $user->sources->pluck('id')->toArray();
            $authors    = $user->authors->pluck('id')->toArray();

            $query->where(
                function ($q) use ($categories, $sources, $authors) {
                    if (!empty($categories)) {
                        $q->orWhereIn('category_id', $categories);
                    }
                    if (!empty($sources)) {
                        $q->orWhereIn('source_id', $sources);
                    }
                    if (!empty($authors)) {
                        $q->orWhereIn('author_id', $authors);
                    }
                }
            );
        }

        $articles = $query->paginate(16, ['*'], 'page', $request->get('page', 1));

        return ArticleResource::collection($articles);
    }

    public function article(Article $article): ArticleResource {
        return new ArticleResource($article);
    }
}
