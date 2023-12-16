<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::controller(UserController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::get('search', 'search');
});

Route::get('post/{post}', [PostController::class, 'show']);
Route::get('post/dashboard', [PostController::class, 'index']);

Route::middleware('auth:api')->group(function () {    
    Route::prefix('profile')->group(function () {
        Route::controller(UserController::class)->group(function () {
            Route::post('logout', 'logout');
            Route::post('', 'update');        
        });
        
        Route::controller(FollowController::class)->group(function () {
            Route::post('{user}/follow', 'follow');
            Route::post('{user}/unfollow', 'unfollow');
            Route::get('following', 'following');
            Route::get('follower', 'follower');
        });
    });
    
    Route::prefix('post')->group(function () {
        Route::controller(PostController::class)->group(function () {
            Route::post('new', 'store');
            Route::get('home', 'home');
        });
    
        Route::prefix('{post}')->group(function () {
            Route::controller(PostController::class)->group(function () {
                Route::post('like', 'like');
                Route::get('', 'show');
                Route::delete('', 'destroy');    
            });
    
            Route::controller(CommentController::class)->prefix('comment')->group(function () {
                Route::delete('{comment}', 'destroy');
                Route::post('{comment}/edit', 'update');
                Route::post('reply/{comment}', 'reply');
                Route::post('', 'store');
            });
        });
    });
});

