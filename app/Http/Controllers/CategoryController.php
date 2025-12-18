<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Cache::remember('categories.active', 3600, function () {
            return Category::active()
                ->orderBy('priority', 'asc')
                ->orderBy('id', 'asc')
                ->get();
        });

        return CategoryResource::collection($categories);
    }
    public function showCategoryWithSub()
    {
        $cacheKey = "categories_with_sub";

        // Fetch from cache or DB
        $categories = Category::with(['sub_categories' => function ($q) {
            $q->active()->orderBy('id', 'asc'); 
        }])
            ->active()
            ->orderBy('priority', 'asc') 
            ->orderBy('id', 'asc')
            ->get();


        // Check if empty
        if ($categories->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No categories found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }
}
