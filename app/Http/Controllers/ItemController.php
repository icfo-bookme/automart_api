<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class ItemController extends Controller
{
    public function searchByCategoryAndName(Request $request)
    {
        $request->validate([
            'category_id' => 'nullable|integer',
            'search'      => 'nullable|string'
        ]);

        $items = (function () use ($request) {
            $query = Item::active()
                ->whereHas('latestStock', function ($q) {
                    $q->where('isPublic', 1);
                });

            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            $searchTerm = $request->search;
            $results = collect();

            if ($request->filled('search')) {
                // First try exact match
                $exactQuery = clone $query;
                $exactResults = $exactQuery->where('name', 'LIKE', '%' . $searchTerm . '%')
                    ->orderBy('id', 'asc')
                    ->limit(20)
                    ->get(['id', 'name', 'sales_price', 'regular_price', 'thumbnail']);

                $results = $exactResults;

                // If no exact matches, try fuzzy search
                if ($exactResults->isEmpty()) {
                    $allItems = $query->get(['id', 'name', 'sales_price', 'regular_price', 'thumbnail', 'category_id']);

                    $matchedItems = $allItems->filter(function ($item) use ($searchTerm) {
                        return $this->calculateSimilarity($item->name, $searchTerm) >= 50;
                    })
                        ->sortByDesc(function ($item) use ($searchTerm) {
                            return $this->calculateSimilarity($item->name, $searchTerm);
                        })
                        ->take(20)
                        ->values();

                    // Add category filter if provided
                    if ($request->filled('category_id')) {
                        $matchedItems = $matchedItems->filter(function ($item) use ($request) {
                            return $item->category_id == $request->category_id;
                        });
                    }

                    // If still no results with category filter, show across all categories
                    if ($request->filled('category_id') && $matchedItems->isEmpty()) {
                        $allItems = Item::active()
                            ->whereHas('latestStock', function ($q) {
                                $q->where('isPublic', 1);
                            })
                            ->get(['id', 'name', 'sales_price', 'regular_price', 'thumbnail']);

                        $matchedItems = $allItems->filter(function ($item) use ($searchTerm) {
                            return $this->calculateSimilarity($item->name, $searchTerm) >= 50;
                        })
                            ->sortByDesc(function ($item) use ($searchTerm) {
                                return $this->calculateSimilarity($item->name, $searchTerm);
                            })
                            ->take(20)
                            ->values();
                    }

                    $results = $matchedItems;
                }
            } else {
                // If no search term, return regular filtered results
                $results = $query->orderBy('id', 'asc')
                    ->limit(20)
                    ->get(['id', 'name', 'sales_price', 'regular_price', 'thumbnail']);
            }

            return $results;
        })();

        // Add similarity score to response for debugging/transparency
        if ($request->filled('search') && !$items->isEmpty()) {
            $items = $items->map(function ($item) use ($request) {
                $itemArray = $item->toArray();
                if ($request->search) {
                    $itemArray['similarity_score'] = $this->calculateSimilarity($item->name, $request->search);
                }
                return $itemArray;
            });
        }

        if ($items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No items found',
                'suggestion' => $this->getSearchSuggestions($request->search)
            ], 404);
        }

        return response()->json([
            'success' => true,
            'did_you_mean' => $this->didYouMeanSuggestion($items, $request->search),
            'data' => $items
        ]);
    }

    /**
     * Calculate similarity percentage between two strings
     */
    private function calculateSimilarity(string $string1, string $string2): float
    {
        $string1 = strtolower($string1);
        $string2 = strtolower($string2);

        // Use PHP's similar_text function
        similar_text($string1, $string2, $percent);

        // Also consider levenshtein distance for typos
        $maxLength = max(strlen($string1), strlen($string2));
        if ($maxLength > 0) {
            $levenshtein = levenshtein($string1, $string2);
            $levenshteinPercent = (1 - ($levenshtein / $maxLength)) * 100;

            // Take the average of both methods
            $percent = ($percent + max(0, $levenshteinPercent)) / 2;
        }

        return $percent;
    }

    /**
     * Generate "Did you mean?" suggestions
     */
    private function didYouMeanSuggestion($items, $searchTerm): ?string
    {
        if (!$searchTerm || $items->isEmpty()) {
            return null;
        }

        // Get the item with highest similarity score
        $bestMatch = $items->sortByDesc(function ($item) use ($searchTerm) {
            return $this->calculateSimilarity($item['name'], $searchTerm);
        })->first();

        $similarity = $this->calculateSimilarity($bestMatch['name'], $searchTerm);

        // Only suggest if similarity is reasonable but not perfect
        if ($similarity < 90 && $similarity > 50) {
            return "Did you mean: " . $bestMatch['name'] . "?";
        }

        return null;
    }

    /**
     * Get alternative search suggestions
     */
    private function getSearchSuggestions(?string $searchTerm): array
    {
        if (!$searchTerm) {
            return [];
        }

        $suggestions = [];

        // Check for common typos or similar words
        $commonTypos = [
            'hunda' => 'honda',
            'hundai' => 'hyundai',
            'toyato' => 'toyota',
            'ferari' => 'ferrari',
            'bently' => 'bentley',
            'maclaren' => 'mclaren',
            'mersedes' => 'mercedes',
            'beamer' => 'bmw',
            'porche' => 'porsche',
            'lexus' => 'lexus',
        ];

        if (isset($commonTypos[strtolower($searchTerm)])) {
            $suggestions[] = $commonTypos[strtolower($searchTerm)];
        }

        // Get popular search terms from database
        $popularSearches = Item::active()
            ->whereHas('latestStock', function ($q) {
                $q->where('isPublic', 1);
            })
            ->select('name')
            ->orderBy('views', 'desc')
            ->limit(10)
            ->pluck('name')
            ->toArray();

        // Find similar popular searches
        foreach ($popularSearches as $popular) {
            if ($this->calculateSimilarity($popular, $searchTerm) > 40) {
                $suggestions[] = $popular;
            }
        }

        return array_unique($suggestions);
    }
    public function latestItem($sectionId)
    {
        $query = Item::with(['subCategory', 'latestStock'])
            ->active()
            ->where('section_id', $sectionId)
            ->whereHas('latestStock', function ($q) {
                $q->where('isPublic', 1);
            });

        if ($sectionId != 1) {
            $query->orderBy('id', 'desc');
        }

        $latestItem = $query->take(30)->get();

        return response()->json([
            'success' => true,
            'data' => $latestItem
        ]);
    }

    public function Item($Id)
    {
        $Item = Item::with(['category', 'specifications', 'latestStock'])
            ->active()
            ->where('id', $Id)

            // latest stock must be public
            ->whereHas('latestStock', function ($q) {
                $q->where('isPublic', 1);
            })

            ->first();

        return response()->json([
            'success' => true,
            'data' => $Item
        ]);
    }


    public function allItems(Request $request)
    {
        $page = $request->get('page', 1);

        $latestItem = Item::with(['subCategory', 'latestStock'])
            ->active()

            // latest stock must be public
            ->whereHas('latestStock', function ($q) {
                $q->where('isPublic', 1);
            })

            ->orderBy('id', 'desc')
            ->paginate(30);

        return response()->json([
            'success' => true,
            'data' => $latestItem
        ]);
    }

    public function getProductsByCategory($categoryId)
    {
        $products = Item::with(['subCategory', 'latestStock'])
            ->active()
            ->where('category_id', $categoryId)
            ->whereHas('latestStock', function ($q) {
                $q->where('isPublic', 1);
            })
            ->orderBy('id', 'desc')
            ->paginate(30);

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    public function getProdutsBySubCategory($subCategoryId)
    {
        $products = Item::with(['subCategory', 'latestStock'])
            ->active()
            ->where('sub_category_id', $subCategoryId)

            // latest stock must be public
            ->whereHas('latestStock', function ($q) {
                $q->where('isPublic', 1);
            })

            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }


    public function sectionItems(Request $request, $id)
    {
        $page = $request->get('page', 1);

        $latestItem = Item::with(['subCategory', 'latestStock'])
            ->active()
            ->where('section_id', $id)

            // latest stock must be public
            ->whereHas('latestStock', function ($q) {
                $q->where('isPublic', 1);
            })

            ->orderBy('id', 'desc')
            ->paginate(30);

        return response()->json([
            'success' => true,
            'data' => $latestItem
        ]);
    }

    public function offerItems(Request $request)
    {
        $page = $request->get('page', 1);

        $latestItem = Item::with(['subCategory', 'latestStock'])
            ->active()

            // latest stock must be public
            ->whereHas('latestStock', function ($q) {
                $q->where('isPublic', 1);
            })

            ->orderBy('id', 'asc')
            ->paginate(30);

        return response()->json([
            'success' => true,
            'data' => $latestItem
        ]);
    }

    
}
