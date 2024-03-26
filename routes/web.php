<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppController;
use App\Http\Controllers\UserController;

Route::get('/', [AppController::class, 'index']);
Route::get('/users', [UserController::class, 'list']);
