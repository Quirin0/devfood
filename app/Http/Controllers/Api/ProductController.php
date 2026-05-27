<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::query()->with(['restaurant', 'foodCategory']);

        if ($request->filled('category')) {
            $query->whereHas('foodCategory', fn ($q) => $q->where('slug', $request->string('category')));
        }

        if ($request->filled('restaurant')) {
            $query->whereHas('restaurant', fn ($q) => $q->where('slug', $request->string('restaurant')));
        }

        if ($request->boolean('recommended')) {
            $query->where(function ($q) {
                $q->where('is_popular', true)->orWhere('is_promo', true);
            });
        }

        if ($request->boolean('promo')) {
            $query->where('is_promo', true);
        }

        if ($request->boolean('popular')) {
            $query->where('is_popular', true);
        }

        $products = $query
            ->orderByDesc('is_popular')
            ->orderBy('sort_order')
            ->limit($request->integer('limit', 50))
            ->get();

        return response()->json(['data' => $products]);
    }
}
