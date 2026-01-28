<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;

Route::prefix('v1')->group(function () {
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories-with-sub', [CategoryController::class, 'showCategoryWithSub']);
    Route::get('/items/search', [ItemController::class, 'searchByCategoryAndName']);
    Route::get('/items/{sectionId}', [ItemController::class, 'latestItem']);
    Route::get('/items', [ItemController::class, 'allItems']);
    Route::get('/item/{Id}', [ItemController::class, 'Item']);
    Route::get('/items/subcategory/{subCategoryId}', [ItemController::class, 'getProdutsBySubCategory']);
    Route::get('/offers/items', [ItemController::class, 'offerItems']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::post('/contact/store', [ContactController::class, 'store']);
});
