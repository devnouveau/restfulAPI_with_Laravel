<?php

use App\Http\Controllers\Auth;
use App\Http\Controllers\User;
use App\Http\Controllers\HomeController;
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

// TODO : 현재 임시 구현된 상태로, 추후에는 jetstram 사용하여 프론트엔드 인증 구현할 것
// (laravel8에서 기본으로 지원되지 않는 laravel/ui auth 스캐폴딩(강의에서 구현에 사용한 것)을 사용하여, 프론트엔드 구현이 미흡한 상태)

// Authentication Routes...
Route::get('login', [Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [Auth\LoginController::class, 'login']);
Route::post('logout', [Auth\LoginController::class, 'logout'])->name('logout');
Route::get('register', [User\UserController::class, 'showRegisterForm'])->name('register')->middleware('guest');
Route::post('register', [User\UserController::class, 'store'])->middleware('guest');

// Password Reset Routes...
Route::get('password/reset', [Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [Auth\ResetPasswordController::class, 'reset'])->name('password.update');

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::view('/', 'welcome')->middleware('guest');
