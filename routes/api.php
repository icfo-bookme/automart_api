<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\SectionController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
   Route::put('/change-password', [AuthController::class, 'changePassword']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
});


Route::prefix('v1')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {      
         Route::get('/my-orders', [OrderController::class, 'myOrders']);
        
    });
    Route::get('/sections', [SectionController::class, 'index']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories-with-sub', [CategoryController::class, 'showCategoryWithSub']);
    Route::get('/items/search', [ItemController::class, 'searchByCategoryAndName']);
    Route::get('/items/{sectionId}', [ItemController::class, 'latestItem']);
    Route::get('/items', [ItemController::class, 'allItems']);
    Route::get('/item/{Id}', [ItemController::class, 'Item']);
    Route::get('/items/subcategory/{subCategoryId}', [ItemController::class, 'getProdutsBySubCategory']);
    Route::get('/offers/items', [ItemController::class, 'offerItems']);
    Route::get('/sections/{id}', [ItemController::class, 'sectionItems']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::post('/contact/store', [ContactController::class, 'store']);
    Route::get('/reviews/{itemId}', [RatingController::class, 'index']);
    Route::post('/reviews', [RatingController::class, 'store']);
    Route::post('/request-a-product', [ItemController::class, 'requestProduct']);
});
