<?php

namespace App\Http\Resources\Iam\User;

use App\Http\Resources\Misc\AuthorResource;
use App\Http\Resources\Misc\CategoryResource;
use App\Http\Resources\Misc\SourceResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'categories' => CategoryResource::collection($this->categories),
            'sources'    => SourceResource::collection($this->sources),
            'authors'    => AuthorResource::collection($this->authors),
        ];
    }
}
