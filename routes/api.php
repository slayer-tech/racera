<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\AuthController;

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

Route::group(['as' => 'api.'], function() {
    Route::group(['middleware' => 'auth:sanctum'], function() {
        Route::post('/description/edit', [\App\Http\Controllers\Api\ProfileController::class, 'editDescription'])->name('description.edit');
    });

    Route::group(['prefix' => 'auth', 'as' => 'auth.'], function() {
        Route::post('/signup', [AuthController::class, 'signup'])->name('signup');
        Route::post('/login', [AuthController::class, 'login'])->name('login');

        Route::group(['middleware' => 'auth:sanctum'], function() {
            Route::get('logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('user', [AuthController::class, 'user'])->name('user');
        });
    });
});
