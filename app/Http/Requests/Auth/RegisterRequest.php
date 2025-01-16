<?php

namespace App\Http\Requests\Admin\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'name'     => 'required',
            'email'    => 'required|unique:users',
            'password' => 'required|confirmed|min:6'
        ];
    }
}
