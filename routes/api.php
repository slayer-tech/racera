<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\ClanController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\UpgradeController;
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
        Route::group(['prefix' => 'profile', 'as' => 'profile.'], function () {
            Route::get('/', [ProfileController::class, 'index'])->name('index');
            Route::post('/edit', [ProfileController::class, 'update'])->name('update');
        });

        Route::group(['as' => 'car.'], function () {
            Route::get('/cars', [CarController::class, 'index'])->name('index');
            Route::group(['prefix' => 'car'], function () {
                Route::get('/{id}', [CarController::class, 'show'])->name('show');
                Route::get('/buy/{id}', [CarController::class, 'buy'])->name('buy');
                Route::get('/sell/{id}', [CarController::class, 'sell'])->name('sell');
            });
        });

        Route::group(['as' => 'upgrades.'], function () {
            Route::get('/upgrades', [UpgradeController::class, 'index'])->name('index');
            Route::group(['prefix' => 'car'], function () {
                Route::get('/buy/{id}', [UpgradeController::class, 'buy'])->name('buy');
                Route::get('/sell/{id}', [UpgradeController::class, 'sell'])->name('sell');
            });
        });

        Route::group(['as' => 'clan.'], function () {
            Route::get('/clans', [ClanController::class, 'index'])->name('index');
            Route::group(['prefix' => 'clan'], function () {
                Route::get('/{id}', [ClanController::class, 'show'])->name('show');
                Route::patch('/{id}', [ClanController::class, 'update'])->name('update');
            });
        });

        Route::group(['as' => 'chat.'], function () {
            Route::get('/chats', [ChatController::class, 'index'])->name('index');
            Route::group(['prefix' => 'chat'], function() {
                Route::get('/{id}', [ChatController::class, 'show'])->name('show');
            });
        });

        Route::post('/message/store', [MessageController::class, 'store'])->name('message.store');
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
