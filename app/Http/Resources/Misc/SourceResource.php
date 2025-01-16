<?php

namespace App\Http\Resources\Misc;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SourceResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id'   => $this->id,
            'name' => $this->name,
            'url'  => $this->url
        ];
    }
}
