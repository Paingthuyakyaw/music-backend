<?php

use App\Http\Controllers\AlbumController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\Auth\AdminController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\FavouriteController;
use App\Http\Controllers\MusicController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\UserMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;



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

    Route::get('all-users', [UserController::class,'index'])->middleware(['auth:sanctum']);


    // musi
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
        Route::put('/{id}', 'update')->middleware(['auth:sanctum']);
        Route::get('/{id}', 'show');
        Route::delete('/{id}', 'destroy')->middleware(['auth:sanctum']);
    });

    // album
    Route::prefix('/album')->controller(AlbumController::class)->group(function () {
        Route::post('/', 'store')->middleware(['auth:sanctum']);
        Route::get('/', 'index');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update')->middleware(['auth:sanctum']);
        Route::delete('/{id}', 'destroy')->middleware(['auth:sanctum']);
    });

    // favourite
    Route::prefix('/favourite')->middleware(['auth:sanctum'])->controller(FavouriteController::class)->group(function () {
        Route::get('/','index');
        Route::post('/','store');
        Route::delete('/{id}','destroy');
    } );

    // logout
    Route::get('/logout',[AuthController::class,'logout'])->middleware(['auth:sanctum']);
    Route::get('/me',[UserController::class,'me'])->middleware(['auth:sanctum']);

});
