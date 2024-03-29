<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AppController::class, 'index']);
Route::get('/users', [UserController::class, 'list']);
Route::get('/auth_test', [UserController::class, 'list'])->middleware('auth');
Route::post('/login_endpoint', [LoginController::class, 'authenticate']);
Route::get('/login', [LoginController::class, 'displayLogin'])->name('login');
