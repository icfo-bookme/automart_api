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
            'category_id' => 'nullable|integer', // make nullable
            'search'      => 'nullable|string'
        ]);

        $cacheKey = 'items_search_cat_' . ($request->category_id ?? 'all')
            . '_search_' . md5($request->search ?? '');

        // ⏱ cache time (10 minutes)
        $cacheTime = 600;

        $items = Cache::remember($cacheKey, $cacheTime, function () use ($request) {

            $query = Item::query()
                ->where('is_published', 1);

            if ($request->filled('category_id')) {
                // first try filtering by category
                $query->where('category_id', $request->category_id);
            }

            if ($request->filled('search')) {
                $query->where('name', 'LIKE', '%' . $request->search . '%');
            }

            $result = $query
                ->orderBy('id', 'asc')
                ->limit(20)
                ->get([
                    'id',
                    'name',
                    'sales_price',
                    'regular_price',
                    'thumbnail'
                ]);

            // fallback: if category_id was given but no results found, search all categories
            if ($request->filled('category_id') && $result->isEmpty()) {
                $fallbackQuery = Item::query()
                    ->where('is_published', 1);

                if ($request->filled('search')) {
                    $fallbackQuery->where('name', 'LIKE', '%' . $request->search . '%');
                }

                return $fallbackQuery
                    ->orderBy('id', 'asc')
                    ->limit(20)
                    ->get([
                        'id',
                        'name',
                        'sales_price',
                        'regular_price',
                        'thumbnail'
                    ]);
            }

            return $result;
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

    public function latestItem()
{
    $latestItem = Cache::remember('items.latest', 3600, function () {
        return Item::active()->where('section_id', 2)
            ->orderBy('id', 'desc')
            ->take(30)
            ->get();
    });

    return response()->json([
        'success' => true,
        'data' => $latestItem
    ]);
}

}
