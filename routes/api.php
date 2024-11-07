<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OpenFoodController;
use App\Http\Controllers\AuthController;

// Rota para o login
Route::post('/login', [AuthController::class, 'login']);

Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/refresh', [AuthController::class, 'refresh']);


    Route::prefix('openfood')->group(function () {
        Route::get('/product/{barcode}', [OpenFoodController::class, 'getProductByBarcode']);
        Route::get('/search', [OpenFoodController::class, 'searchProducts']);
        Route::get('/ingredient', [OpenFoodController::class, 'getProductIngredientsOCR']);
        Route::get('/suggestions', [OpenFoodController::class, 'getSuggestions']);
        Route::get('/nutrients', [OpenFoodController::class, 'getNutrients']);
        Route::get('/attribute-groups', [OpenFoodController::class, 'getAttributeGroups']);
        Route::get('/preferences', [OpenFoodController::class, 'getPreferences']);


        Route::post('/product', [OpenFoodController::class, 'addOrEditProduct']);
        Route::post('/product/photo', [OpenFoodController::class, 'addProductPhoto']);
        Route::post('/rotate-photo', [OpenFoodController::class, 'rotateProductPhoto']);
        Route::post('/crop-photo', [OpenFoodController::class, 'cropProductPhoto']);

    });
});
