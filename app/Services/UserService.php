<?php

namespace App\Services;

use App\Http\Requests\UserPreferenceRequest;
use App\Models\User;

class UserService {
    public function savePreferences(User $user, UserPreferenceRequest $request): User {
        $validatedRequest = $request->validated();

        if (isset($validatedRequest['category_ids'])) {
            $user->categories()->sync($validatedRequest['category_ids']);
        } else {
            $user->categories()->sync([]);
        }

        if (isset($validatedRequest['author_ids'])) {
            $user->authors()->sync($validatedRequest['author_ids']);
        } else {
            $user->authors()->sync([]);
        }

        if (isset($validatedRequest['source_ids'])) {
            $user->sources()->sync($validatedRequest['source_ids']);
        } else {
            $user->sources()->sync([]);
        }

        return $user->load(['categories', 'sources', 'authors']);
    }
}
