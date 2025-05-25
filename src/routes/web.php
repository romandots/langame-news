<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SseController;
use App\Http\Middlewares\EnsureUserIsConfirmed;
use Illuminate\Support\Facades\Route;



Route::get('/register', [RegistrationController::class, 'showRegistrationForm'])
    ->name('register');
Route::post('/register', [RegistrationController::class, 'submitRegistration'])
    ->name('register.submit');

Route::get('/login', [LoginController::class, 'showLoginForm'])
    ->name('login');
Route::post('/login', [LoginController::class, 'login'])
    ->name('login.submit');

Route::middleware('auth')->group(function () {
    Route::get('/register/confirm', [RegistrationController::class, 'showConfirmationForm'])
        ->name('register.confirm');
    Route::get('/register/confirm/resend', [RegistrationController::class, 'resendConfirmationCode'])
        ->name('register.confirm.resend');
    Route::post('/register/confirm', [RegistrationController::class, 'submitConfirmation'])
        ->name('register.confirm.submit');

    Route::get('/logout', [LoginController::class, 'logout'])
        ->name('logout');

    Route::middleware(EnsureUserIsConfirmed::class)->group(function () {
        Route::get('/', [NewsController::class, 'index'])->name('home');
        Route::get('/fetch', [NewsController::class, 'fetch'])->name('news.fetch');

        Route::get('/users', [UserController::class, 'index'])->name('users');
        Route::get('/users/fetch', [UserController::class, 'fetch'])->name('users.fetch');

        Route::get('/sse', [SseController::class, 'stream'])->name('sse');
    });
});
