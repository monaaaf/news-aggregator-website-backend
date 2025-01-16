<?php

namespace App\Http\Resources;

use App\Http\Resources\Misc\AuthorResource;
use App\Http\Resources\Misc\CategoryResource;
use App\Http\Resources\Misc\SourceResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id'             => $this->id,
            'title'          => $this->title,
            'trail_text'     => $this->trail_text,
            'stand_first'    => $this->stand_first,
            'main'           => $this->main,
            'content'        => $this->content,
            'url'            => $this->url,
            'featured_image' => $this->featured_image,
            'published_at'   => $this->published_at,
            'source'         => new SourceResource($this->source),
            'category'       => new CategoryResource($this->category),
            'author'         => new AuthorResource($this->author),
        ];
    }
}
