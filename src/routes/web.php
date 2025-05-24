<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;


Route::get('/register', [RegistrationController::class, 'showRegistrationForm'])
    ->name('register');
Route::post('/register', [RegistrationController::class, 'submitRegistration'])
    ->name('register.submit');
Route::get('/register/confirm', [RegistrationController::class, 'showConfirmationForm'])
    ->name('register.confirm');
Route::post('/register/confirm', [RegistrationController::class, 'submitConfirmation'])
    ->name('register.confirm.submit');

Route::get('/login', [LoginController::class, 'showLoginForm'])
    ->name('login');
Route::post('/login', [LoginController::class, 'login'])
    ->name('login.submit');

Route::middleware([
    'auth',
    \App\Http\Middlewares\EnsureUserIsConfirmed::class,
    ])
    ->group(function () {
        Route::get('/', function () {
            return view('welcome');
        })->name('home');

        Route::get('/logout', [LoginController::class, 'logout'])
            ->name('logout');
    });
