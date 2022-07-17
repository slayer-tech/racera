<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\ClanController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\PrivilegeController;
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
        Route::get('/profiles', [ProfileController::class, 'index'])->name('index');
        Route::group(['prefix' => 'profile', 'as' => 'profile.'], function () {
            Route::get('/{id}', [ProfileController::class, 'show'])->name('show');
            Route::get('/find/{name}', [ProfileController::class, 'find'])->name('find');
            Route::put('/edit', [ProfileController::class, 'update'])->name('update');
        });

        Route::group(['as' => 'car.'], function () {
            Route::get('/cars', [CarController::class, 'index'])->name('index');
            Route::group(['prefix' => 'car'], function () {
                Route::get('/{id}', [CarController::class, 'show'])->name('show');
                Route::post('/{id}', [CarController::class, 'buy'])->name('buy');
                Route::delete('/{id}', [CarController::class, 'sell'])->name('sell');
            });
        });

        Route::group(['as' => 'upgrades.'], function () {
            Route::get('/upgrades', [UpgradeController::class, 'index'])->name('index');
            Route::group(['prefix' => 'car'], function () {
                Route::post('/{id}', [UpgradeController::class, 'buy'])->name('buy');
                Route::delete('/{id}', [UpgradeController::class, 'sell'])->name('sell');
            });
        });

        Route::group(['as' => 'privilege'], function () {
            Route::get('/privileges', [PrivilegeController::class, 'index'])->name('index');
            Route::group(['prefix' => 'privilege'], function () {
                Route::get('/{id}', [PrivilegeController::class, 'show'])->name('show');
                Route::post('/{id}', [PrivilegeController::class, 'buy'])->name('buy');
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
