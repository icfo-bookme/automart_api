<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::active()
            ->orderBy('priority', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return CategoryResource::collection($categories);
    }
    public function showCategoryWithSub()
    {
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
