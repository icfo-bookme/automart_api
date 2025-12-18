<?php

use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;

Route::prefix('v1')->group(function () {
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories-with-sub', [CategoryController::class, 'showCategoryWithSub']);
    Route::get('/items/search', [ItemController::class, 'searchByCategoryAndName']);

});
