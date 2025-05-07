<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'city' => 'required|string|max:255',
            'region' => 'required|string|max:255',
            'adresse' => 'required|string',
            'postal_code' => 'required|string|max:20',
        ]);

        $cart = $user->cart;
        $items = $cart->items;

        if ($items->isEmpty()) {
            return response()->json(['error' => 'Cart is empty'], 400);
        }

        DB::beginTransaction();

        try {
            $order = Order::create([
                'user_id' => $user->id,
                'city' => $validated['city'],
                'region' => $validated['region'],
                'adresse' => $validated['adresse'],
                'postal_code' => $validated['postal_code'],
                'status' => 'pending',
            ]);

            foreach ($items as $item) {
                $productSize = ProductSize::where('product_id', $item->product_id)
                    ->where('size', $item->size)
                    ->first();

                if (!$productSize || $item->quantity > $productSize->stock) {
                    DB::rollBack();
                    return response()->json(['error' => 'Stock issue with ' . $item->product->title], 400);
                }

                $productSize->decrement('stock', $item->quantity);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'size' => $item->size,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);
            }

            $cart->items()->delete();

            DB::commit();

            return response()->json([
                'message' => 'Order placed successfully',
                'order' => $order,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Order failed', 'details' => $e->getMessage()], 500);
        }
    }
    public function myOrders()
    {
        $user = Auth::user();

        $orders = Order::with('items.product')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return response()->json(['orders' => $orders]);
    }
    public function index()
    {
        // $user = Auth::user();

        // if ($user->role !== 'admin') {
        //     return response()->json(['error' => 'Unauthorized'], 403);
        // }

        $orders = Order::with('user', 'items.product')->latest()->get();

        return response()->json(['orders' => $orders]);
    }
    public function updateStatus(Request $request, $orderId)
    {
        // $user = Auth::user();

        // if ($user->role !== 'admin') {
        //     return response()->json(['error' => 'Unauthorized'], 403);
        // }

        $validated = $request->validate([
            'status' => 'required|in:pending,validated,being delivered',
        ]);

        $order = Order::findOrFail($orderId);
        $order->status = $validated['status'];
        $order->save();

        return response()->json(['message' => 'Order status updated']);
    }
    public function destroy($orderId)
    {
        // $user = Auth::user();

        // if ($user->role !== 'admin') {
        //     return response()->json(['error' => 'Unauthorized'], 403);
        // }

        $order = Order::findOrFail($orderId);
        $order->delete();

        return response()->json(['message' => 'Order deleted']);
    }
}
