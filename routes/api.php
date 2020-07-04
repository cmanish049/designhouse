<?php

use Illuminate\Support\Facades\Route;

// public routes

// routes for authenticated users
Route::group(['middleware' => ['auth:api']], function() {
    Route::post('', []);
});

// routes for guests only
Route::group(['middleware' => ['guest:api']], function() {
    Route::post('register', [Auth\RegisterColtroller::class, 'register']);
});




