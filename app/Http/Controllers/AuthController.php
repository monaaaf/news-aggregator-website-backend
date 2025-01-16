<?php

namespace App\Http\Controllers;

use App\Http\Resources\Iam\User\UserResource;
use App\Services\AuthService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller {
    private AuthService $authService;

    public function __construct() {
        $this->authService = new AuthService();
    }

    /**
     * @throws Exception
     */
    public function login(Request $request): UserResource {
        $user = $this->authService->login($request);

        return (new UserResource($user))->additional(['token' => $user->createToken('API_TOKEN')->plainTextToken]);
    }

    public function register(Request $request): UserResource {
        $user = $this->authService->register($request);

        return (new UserResource($user))->additional(['token' => $user->createToken('API_TOKEN')->plainTextToken]);
    }

    public function profile(): UserResource {
        return new UserResource(Auth::user());
    }
}
