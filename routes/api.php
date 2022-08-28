<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\ClanController;
use App\Http\Controllers\Api\GameController;
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
    Route::group(['middleware' => 'auth:sanctum',], function () {

        Route::apiResource('profiles', ProfileController::class)->only([
            'index', 'show'
        ]);
        Route::get('/profiles/search/{name}', [ProfileController::class, 'search'])->name('profiles.search');
        Route::patch('/profiles', [ProfileController::class, 'update'])->name('profiles.update');

        Route::apiResource('cars', CarController::class)->only([
            'index', 'show'
        ]);
        Route::group(['as' => 'cars.', 'prefix' => 'cars'], function () {
            Route::post('/{id}', [CarController::class, 'buy'])->name('buy');
            Route::delete('/{id}', [CarController::class, 'sell'])->name('sell');
        });

        Route::group(['as' => 'upgrades.', 'prefix' => 'upgrades'], function () {
            Route::get('/', [UpgradeController::class, 'index'])->name('index');
            Route::post('/{id}', [UpgradeController::class, 'buy'])->name('buy');
            Route::delete('/{id}', [UpgradeController::class, 'sell'])->name('sell');
        });

        Route::apiResource('privileges', PrivilegeController::class)->only([
            'index', 'show'
        ]);
        Route::post('/privileges/{id}', [PrivilegeController::class, 'buy'])->name('privileges.buy');

        Route::apiResource('clans', ClanController::class)->only([
            'index', 'update', 'show', 'store'
        ]);
        Route::get('/clans/search/{name}', [ClanController::class, 'search'])->name('clans.search');

        Route::group(['as' => 'chats.', 'prefix' => 'chats'], function () {
            Route::get('/', [ChatController::class, 'index'])->name('index');
            Route::get('/{recipient_id}', [ChatController::class, 'show'])->name('show');
        });


        Route::group(['as' => 'game.', 'prefix' => 'game'], function () {
            Route::get('/walls/generate', [GameController::class, 'generateWalls'])->name('walls.generate');
            Route::get('/end', [GameController::class, 'end'])->name('end');
        });

        Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    });

    Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
        Route::group(['middleware' => 'guest:sanctum'], function () {
            Route::post('/signup', [AuthController::class, 'signup'])->name('signup');
            Route::post('/login', [AuthController::class, 'login'])->name('login');
        });

        Route::group(['middleware' => 'auth:sanctum'], function () {
            Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('/user', [AuthController::class, 'user'])->name('user');
        });
    });
});
