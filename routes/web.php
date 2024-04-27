<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AppController::class, 'index'])->name('index');
Route::post('/cart/modify', [AppController::class, 'modify_cart'])->middleware('auth');
Route::get('/order/confirm', [OrderController::class, 'confirm_order'])->middleware('auth');
Route::post('/order/submit', [OrderController::class, 'submit_order'])->middleware('auth');
Route::get('/order/list', [OrderController::class, 'list_orders'])->middleware('auth')->name('list_orders');
Route::get('/users', [UserController::class, 'list']);
Route::get('/auth_test', [UserController::class, 'list'])->middleware('auth');
Route::post('/login_endpoint', [LoginController::class, 'authenticate']);
Route::get('/login', [LoginController::class, 'displayLogin'])->name('login');
Route::post('/signup_endpoint', [LoginController::class, 'register']);
Route::get('/signup', [LoginController::class, 'displaySignup'])->name('signup');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
