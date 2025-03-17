<?php
// routes/api.php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\CompanyController;
use App\Http\Controllers\API\DepositController;
use App\Http\Controllers\API\ItemController;
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

    // Deposits - users can only manage deposits for their own company
    Route::apiResource('deposits', DepositController::class);
    
    // Categories
    Route::apiResource('categories', CategoryController::class);
    Route::get('categories/{category}/items', [CategoryController::class, 'items']);
    
    // Items
    Route::apiResource('items', ItemController::class);
    Route::get('items/search', [ItemController::class, 'search']);
});