<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserPreferenceRequest;
use App\Http\Resources\Iam\User\UserResource;
use App\Models\User;
use App\Services\UserService;

class UserController extends Controller {
    private UserService $userService;

    public function __construct() {
        $this->userService = new UserService();
    }
    public function savePreferences(User $user, UserPreferenceRequest $request): UserResource {
        $user = $this->userService->savePreferences($user, $request);

        return new UserResource($user);
    }
}
