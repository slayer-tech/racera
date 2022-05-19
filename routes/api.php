<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClanController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['as' => 'api.'], function () {
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::group(['prefix' => 'profile','as' => 'profile.'], function () {
            Route::get('/', [ProfileController::class, 'index'])->name('index');
            Route::post('/edit', [ProfileController::class, 'update'])->name('update');
        });

        Route::group(['as' => 'clan.'], function () {
            Route::get('/clans', [ClanController::class, 'index'])->name('index');
            Route::group(['prefix' => 'clan'], function() {
                Route::get('/{id}', [ClanController::class, 'show'])->name('show');
                Route::patch('/{id}', [ClanController::class, 'update'])->name('update');
            });
        });
    });

    Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
        Route::post('/signup', [AuthController::class, 'signup'])->name('signup');
        Route::post('/login', [AuthController::class, 'login'])->name('login');

        Route::group(['middleware' => 'auth:sanctum'], function () {
            Route::get('logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('user', [AuthController::class, 'user'])->name('user');
        });
    });
});
