<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::view('/login', 'login')->name('login');
Route::view('/signup', 'signup');

    Route::view('/game', 'game');

    Route::view('/user', 'user');
    Route::view('/logout', 'logout');

    Route::view('/chats', 'chats');
    Route::view('/chats/{id}', 'chat', ['id']);

    Route::get('/clans/{id}', function (int $id) {
        return view('clan', compact('id'));
    });
