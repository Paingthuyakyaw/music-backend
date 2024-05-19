<?php

use App\Http\Controllers\AlbumController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\Auth\AdminController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\MusicController;
use App\Http\Middleware\UserMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// auth
Route::prefix('/v1/')->group(function () {

    // user auth
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot_password', [AuthController::class, 'forgot_password']);
    Route::post('reset_password', [AuthController::class, "reset_password"])->name("password.reset");


    // admin auth
    Route::post('admin-register', [AdminController::class, 'register']);
    Route::post('admin-login', [AdminController::class, 'login']);


    // music
    Route::prefix('/music')->controller(MusicController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store')->middleware(['auth:sanctum']);
        Route::put('/{id}','update')->middleware(['auth:sanctum']);
        Route::delete('/{id}', 'destroy')->middleware(['auth:sanctum']);
    });

    // artist
    Route::prefix('/artist')->controller(ArtistController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store')->middleware(['auth:sanctum']);
        Route::patch('/{id}', 'update')->middleware(['auth:sanctum']);
        Route::get('/{id}', 'show');
        Route::delete('/{id}', 'destroy')->middleware(['auth:sanctum']);
    });

    // album
    Route::prefix('/album')->controller(AlbumController::class)->group(function () {
        Route::post('/', 'store')->middleware(['auth:sanctum']);
        Route::get('/', 'index');
        Route::get('/{id}', 'show');
        Route::patch('/{id}', 'update')->middleware(['auth:sanctum']);
        Route::delete('/{id}', 'destroy')->middleware(['auth:sanctum']);
    });
});
