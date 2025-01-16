<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AuthService {
    /**
     * @throws Exception
     */
    public function login(Request $request) {
        // first we need to check if the user email and password exist and match
        if (!Auth::attempt($request->only(['email', 'password']))) {
            throw new Exception(__('auth.failed'), Response::HTTP_UNAUTHORIZED);
        }

        return User::whereEmail($request->get('email'))->first();
    }

    public function register(Request $request) {
        $request->validate(
            [
                'name'     => 'required|string',
                'email'    => 'required|string|lowercase|email|max:255|unique:' . User::class,
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]
        );

        return User::create(
            [
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]
        );
    }
}
