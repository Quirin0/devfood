<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->attributes->get('jwt_user');

        $orders = Order::query()
            ->with(['items', 'restaurant'])
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['data' => $orders]);
    }

    public function store(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->attributes->get('jwt_user');

        $validator = Validator::make($request->all(), [
            'restaurant_id' => ['required', 'integer', 'exists:restaurants,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:99'],
            'items.*.notes' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $restaurant = Restaurant::query()->findOrFail($request->integer('restaurant_id'));
        $itemsInput = $request->input('items', []);

        $productIds = collect($itemsInput)->pluck('product_id')->unique()->values();
        $products = Product::query()
            ->whereIn('id', $productIds)
            ->where('restaurant_id', $restaurant->id)
            ->get()
            ->keyBy('id');

        if ($products->count() !== $productIds->count()) {
            return response()->json([
                'message' => 'Alguns produtos não pertencem a este restaurante.',
            ], 422);
        }

        $subtotal = 0;
        $lineItems = [];

        foreach ($itemsInput as $row) {
            $product = $products[$row['product_id']];
            $quantity = (int) $row['quantity'];
            $unitPrice = (float) $product->price;
            $lineTotal = round($unitPrice * $quantity, 2);
            $subtotal += $lineTotal;

            $lineItems[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_image' => $product->image,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'line_total' => $lineTotal,
                'notes' => isset($row['notes']) ? trim((string) $row['notes']) ?: null : null,
            ];
        }

        $deliveryFee = $restaurant->is_free_delivery ? 0.0 : (float) $restaurant->delivery_fee;
        $total = round($subtotal + $deliveryFee, 2);

        $order = DB::transaction(function () use ($user, $restaurant, $subtotal, $deliveryFee, $total, $lineItems) {
            $order = Order::create([
                'user_id' => $user->id,
                'restaurant_id' => $restaurant->id,
                'status' => 'confirmed',
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'total' => $total,
            ]);

            foreach ($lineItems as $line) {
                $order->items()->create($line);
            }

            return $order;
        });

        $order->load(['items', 'restaurant']);

        return response()->json(['data' => $order], 201);
    }
}
