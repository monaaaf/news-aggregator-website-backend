<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GeneralOptionsController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/test', [TestController::class, 'test']);

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::prefix('articles')->group(
    function () {
        Route::get('/', [ArticleController::class, 'articles']);
        Route::get('/{article}', [ArticleController::class, 'article']);
    }
);

Route::prefix('categories')->group(
    function () {
        Route::get('/', [CategoryController::class, 'categories']);
        Route::get('/top', [CategoryController::class, 'topCategories']);
    }
);

Route::prefix('authors')->group(
    function () {
        Route::get('/', [AuthorController::class, 'authors']);
        Route::get('/top', [AuthorController::class, 'topAuthors']);
    }
);

Route::middleware('auth:sanctum')->group(
    function () {
        Route::get('profile', [AuthController::class, 'profile']);

        Route::prefix('options')->group(
            function () {
                Route::get('/', [GeneralOptionsController::class, 'options']);
            }
        );

        Route::prefix('users')->group(
            function () {
                Route::post('/{user}/preferences', [UserController::class, 'savePreferences']);
            }
        );
    }
);
