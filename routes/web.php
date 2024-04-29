<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AppController::class, 'index'])->name('index');

Route::get('/login', [LoginController::class, 'displayLogin'])->name('login');
Route::post('/login_endpoint', [LoginController::class, 'authenticate'])->name('login_endpoint');

Route::get('/signup', [LoginController::class, 'displaySignup'])->name('signup');
Route::post('/signup_endpoint', [LoginController::class, 'register'])->name('signup_endpoint');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/profile', [LoginController::class, 'displayProfile'])->name('profile');
Route::post('/update_profile', [LoginController::class, 'updateProfile'])->name('update_profile');
Route::post('/update_password', [LoginController::class, 'updatePassword'])->name('update_password');

Route::post('/cart/modify', [AppController::class, 'modifyCart'])->middleware('auth')->name('modify_cart');

Route::get('/order/confirm', [OrderController::class, 'confirmOrder'])->middleware('auth')->name('confirm_order');
Route::post('/order/submit', [OrderController::class, 'submitOrder'])->middleware('auth')->name('submit_order');
Route::get('/order/list', [OrderController::class, 'listOrders'])->middleware('auth')->name('list_orders');

