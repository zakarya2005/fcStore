<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $cartItems = CartItem::with('product', 'productSize')
            ->where('cart_id', $user->cart->id)
            ->get();

        return response()->json([
            'items' => $cartItems,
        ]);
    }

    public function addItem(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'size' => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        $cart = $user->cart;

        $productSize = ProductSize::where('product_id', $request->product_id)
            ->where('size', $request->size)
            ->first();

        if (!$productSize) {
            return response()->json(['error' => 'Invalid size for product'], 400);
        }

        if ($request->quantity > $productSize->stock) {
            return response()->json(['error' => 'Not enough stock'], 400);
        }

        // Check if item already in cart
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $request->product_id)
            ->where('size', $request->size)
            ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $request->quantity;

            if ($newQuantity > $productSize->stock) {
                return response()->json(['error' => 'Not enough stock'], 400);
            }

            $cartItem->update([
                'quantity' => $newQuantity,
            ]);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $request->product_id,
                'size' => $request->size,
                'quantity' => $request->quantity,
            ]);
        }

        return response()->json(['message' => 'Item added to cart']);
    }

    public function updateQuantity(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:cart_items,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        $cartItem = CartItem::where('id', $request->item_id)
            ->where('cart_id', $user->cart->id)
            ->firstOrFail();

        $productSize = ProductSize::where('product_id', $cartItem->product_id)
            ->where('size', $cartItem->size)
            ->first();

        if ($request->quantity > $productSize->stock) {
            return response()->json(['error' => 'Not enough stock'], 400);
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return response()->json(['message' => 'Cart item updated']);
    }

    public function removeItem($itemId)
    {
        $user = Auth::user();
        $cartItem = CartItem::where('id', $itemId)
            ->where('cart_id', $user->cart->id)
            ->firstOrFail();

        $cartItem->delete();

        return response()->json(['message' => 'Item removed from cart']);
    }
}
