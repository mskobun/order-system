<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ManuallyEnsureAuth;

Route::get('/', [AppController::class, 'index'])->name('index');
Route::post('/cart/modify', [AppController::class, 'modify_cart']);
Route::get('/order/confirm', [AppController::class, 'confirm_order_page']);
Route::post('/order/submit', [AppController::class, 'submit_order']);
Route::get('/profile', [AppController::class, 'displayProfile'])->name('profile');
Route::post('/update_profile', [LoginController::class, 'updateProfile'])->name('update_profile');
Route::get('/order/{order_id}', [AppController::class, 'order_status'])->name('order_status');
Route::get('/users', [UserController::class, 'list']);
Route::get('/auth_test', [UserController::class, 'list'])->middleware(ManuallyEnsureAuth::class);
Route::post('/login_endpoint', [LoginController::class, 'authenticate']);
Route::get('/login', [LoginController::class, 'displayLogin'])->name('login');
Route::post('/signup_endpoint', [LoginController::class, 'register']);
Route::get('/signup', [LoginController::class, 'displaySignup'])->name('signup');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
