<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Restaurant::query()->with('category');

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->string('category')));
        }

        if ($request->filled('search')) {
            $search = '%'.$request->string('search').'%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhere('category_label', 'like', $search)
                    ->orWhere('description', 'like', $search);
            });
        }

        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        $restaurants = $query
            ->orderByDesc('is_featured')
            ->orderByDesc('rating')
            ->get();

        return response()->json(['data' => $restaurants]);
    }

    public function show(string $slug): JsonResponse
    {
        $restaurant = Restaurant::query()
            ->with(['category', 'products'])
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json(['data' => $restaurant]);
    }
}
