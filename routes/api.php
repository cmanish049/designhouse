<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use Illuminate\Support\Facades\Route;

// public routes
Route::get('me', '\App\Http\Controllers\User\MeController')->name('me');
// routes for authenticated users
Route::group(['middleware' => ['auth:api']], function() {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
});

// routes for guests only
Route::group(['middleware' => ['guest:api']], function() {
    Route::post('register', [RegisterController::class, 'register']);
    Route::post('verification/verify/{user}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('verification/resend', [VerificationController::class, 'resend'])->name('verification.resend');
    Route::post('login', [LoginController::class, 'login'])->name('login');
});




