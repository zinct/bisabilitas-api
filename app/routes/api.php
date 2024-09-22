<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\KBBIController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PageHistoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecommendationPageController;
use Illuminate\Support\Facades\Route;


// Authentication
Route::middleware('guest')->group(function () {
    Route::post('/oauth', [AuthController::class, 'oauth']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'index']);
    Route::put('/profile', [ProfileController::class, 'update']);

    // Pages
    Route::post('/page/favorite/{page}', [PageController::class, 'favorite']);
    Route::post('/page/read/{page}', [PageController::class, 'read']);
    Route::apiResource('/page', PageController::class);


    // Pages Histories
    Route::post('/page-history/{page}', [PageHistoryController::class, 'read']);
    
    // Logout
    Route::get('/logout', [AuthController::class, 'logout']);
});

Route::get('/page-history', [PageHistoryController::class, 'index']);
Route::get('/page-recommendation', [RecommendationPageController::class, 'index']);
Route::post('/kbbi', [KBBIController::class, 'index']);
