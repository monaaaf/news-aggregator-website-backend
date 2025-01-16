<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserPreferenceRequest extends FormRequest {
    public function authorize(): bool {
        return auth()->check();
    }

    public function rules(): array {
        return [
            'category_ids.*' => [
                'distinct',
                'exists:categories,id'
            ],
            'source_ids.*'   => [
                'distinct',
                'exists:sources,id'
            ],
            'author_ids.*'   => [
                'distinct',
                'exists:authors,id'
            ],
        ];
    }
}
