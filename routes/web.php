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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('login');
});

Route::get('/signup', function () {
    return view('signup');
});

Route::get('/user', function () {
    return view('user');
});

Route::get('/chats', function () {
    return view('chats');
});

Route::get('/chat/{id}', function ($id) {
    return view('chat', ['id' => $id]);
});

Route::get('/clan/{id}', function (int $id) {
    return view('clan', compact('id'));
});

Route::get('/logout', function () {
    return view('logout');
});
