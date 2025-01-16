<?php

namespace App\Http\Resources;

use App\Http\Resources\Misc\AuthorResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleCondensedResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id'             => $this->id,
            'title'          => $this->title,
            'featured_image' => $this->featured_image,
            'published_at'   => $this->published_at,
            'author'         => new AuthorResource($this->author),
        ];
    }
}
