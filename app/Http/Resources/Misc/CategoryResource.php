<?php

namespace App\Http\Resources\Misc;

use App\Http\Resources\ArticleCondensedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id'       => $this->id,
            'name'     => $this->name,
            'slug'     => $this->slug,
            'articles' => ArticleCondensedResource::collection($this->articles)
        ];
    }
}
