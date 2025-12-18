<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Cache;

class ItemController extends Controller
{
    public function searchByCategoryAndName(Request $request)
    {
        $request->validate([
            'category_id' => 'required|integer',
            'search'      => 'nullable|string'
        ]);

        $cacheKey = 'items_search_cat_' . $request->category_id
            . '_search_' . md5($request->search ?? '');

        // ⏱ cache time (10 minutes)
        $cacheTime = 600;

        $items = Cache::remember($cacheKey, $cacheTime, function () use ($request) {

            $query = Item::query()
                ->where('category_id', $request->category_id)
                ->where('is_published', 1);

            if ($request->filled('search')) {
                $query->where('name', 'LIKE', '%' . $request->search . '%');
            }

            return $query
                ->orderBy('id', 'desc')
                ->limit(20)
                ->get([
                    'id',
                    'name',
                    'sales_price',
                    'regular_price',
                    'thumbnail'
                ]);
        });

        if ($items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No items found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'from_cache' => Cache::has($cacheKey),
            'data' => $items
        ]);
    }
}
