<?php

namespace App\Http\Controllers;

use App\Http\Resources\Misc\CategoryResource;
use App\Services\CategoryService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller {
    private CategoryService $categoryService;

    public function __construct() {
        $this->categoryService = new CategoryService();
    }

    public function categories(): AnonymousResourceCollection {
        return CategoryResource::collection($this->categoryService->query()->paginate());
    }

    public function topCategories(): AnonymousResourceCollection {
        $topCategories = $this->categoryService->query()
                                               ->withCount('articles')
                                               ->orderBy('articles_count', 'desc')
                                               ->take(2)
                                               ->get();

        return CategoryResource::collection($topCategories);
    }
}
