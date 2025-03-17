<?php
// routes/api.php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\ItemController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Protected routes
Route::group(['middleware' => 'auth:api'], function () {
    // Auth
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('profile', [AuthController::class, 'profile']);

    // Company
    Route::get('company', [CompanyController::class, 'index']);
    Route::get('company/{id}', [CompanyController::class, 'show']);
    Route::put('company', [CompanyController::class, 'update']);
    Route::get('company/users', [CompanyController::class, 'getCompanyUsers']);
    Route::get('company/statistics', [CompanyController::class, 'getCompanyStatistics']);

    // Deposits
    Route::apiResource('deposits', DepositController::class);

   
});