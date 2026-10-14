<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CartService
{
    protected const SESSION_KEY = 'guest_cart_token';

    /**
     * Find or create the cart for the current visitor (logged-in user or guest session).
     */
    public function currentCart(Request $request): Cart
    {
        if ($user = $request->user()) {
            return Cart::firstOrCreate(['user_id' => $user->id]);
        }

        $token = $request->session()->get(self::SESSION_KEY);

        if (! $token) {
            $token = (string) Str::uuid();
            $request->session()->put(self::SESSION_KEY, $token);
        }

        return Cart::firstOrCreate(['session_token' => $token, 'user_id' => null]);
    }

    /**
     * Merge a guest session cart into the user's cart at login time.
     */
   public function mergeGuestCartIntoUser(Request $request, $user): void
{
    $token = $request->session()->get(self::SESSION_KEY);

    if (! $token) {
        return;
    }

    $guestCart = Cart::with('items')
        ->where('session_token', $token)
        ->whereNull('user_id')
        ->first();

    if (! $guestCart || $guestCart->items->isEmpty()) {
        $request->session()->forget(self::SESSION_KEY);

        return;
    }

    $userCart = Cart::firstOrCreate([
        'user_id' => $user->id,
    ]);

    foreach ($guestCart->items as $guestItem) {
        $existing = CartItem::where('cart_id', $userCart->id)
            ->where('product_id', $guestItem->product_id)
            ->where('product_variant_id', $guestItem->product_variant_id)
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Find current product and variant
        |--------------------------------------------------------------------------
        */

        $product = Product::find($guestItem->product_id);

        if (! $product || $product->status !== 'active') {
            continue;
        }

        $variant = null;

        if ($guestItem->product_variant_id) {
            $variant = ProductVariant::where('id', $guestItem->product_variant_id)
                ->where('product_id', $product->id)
                ->where('is_active', true)
                ->first();

            if (! $variant) {
                continue;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Available stock
        |--------------------------------------------------------------------------
        */

        $availableStock = $variant
            ? $variant->stock_quantity
            : $product->stock_quantity;

        $currentQuantity = $existing?->quantity ?? 0;

        $newQuantity = min(
            $currentQuantity + $guestItem->quantity,
            $availableStock
        );

        if ($newQuantity <= 0) {
            continue;
        }

        /*
        |--------------------------------------------------------------------------
        | Use current server-side price
        |--------------------------------------------------------------------------
        */

        $unitPrice = $product->final_price
            + ($variant?->price_adjustment ?? 0);

        if ($existing) {
            $existing->update([
                'quantity' => $newQuantity,
                'unit_price' => $unitPrice,
            ]);
        } else {
            CartItem::create([
                'cart_id' => $userCart->id,
                'product_id' => $product->id,
                'product_variant_id' => $variant?->id,
                'quantity' => min(
                    $guestItem->quantity,
                    $availableStock
                ),
                'unit_price' => $unitPrice,
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Delete guest cart
    |--------------------------------------------------------------------------
    */

    $guestCart->items()->delete();
    $guestCart->delete();

    $request->session()->forget(self::SESSION_KEY);
}

    public function addItem(Cart $cart, Product $product, ?ProductVariant $variant, int $quantity): CartItem
    {
        $this->assertPurchasable($product, $variant, $quantity);

        $unitPrice = $product->final_price + ($variant?->price_adjustment ?? 0);

        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->where('product_variant_id', $variant?->id)
            ->first();

        $newQuantity = ($item?->quantity ?? 0) + $quantity;
        $this->assertStockAvailable($product, $variant, $newQuantity);

        if ($item) {
            $item->update(['quantity' => $newQuantity, 'unit_price' => $unitPrice]);

            return $item;
        }

        return CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'product_variant_id' => $variant?->id,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
        ]);
    }

    public function updateItemQuantity(CartItem $item, int $quantity): void
    {
        if ($quantity < 1) {
            $item->delete();

            return;
        }

        $this->assertStockAvailable($item->product, $item->variant, $quantity);
        $item->update(['quantity' => $quantity]);
    }

    public function removeItem(CartItem $item): void
    {
        $item->delete();
    }

    protected function assertPurchasable(Product $product, ?ProductVariant $variant, int $quantity): void
    {
        if ($product->status !== 'active') {
            throw ValidationException::withMessages(['product' => 'This product is not currently available.']);
        }

        if ($quantity < 1) {
            throw ValidationException::withMessages(['quantity' => 'Quantity must be at least 1.']);
        }

        if ($variant && $variant->product_id !== $product->id) {
            throw ValidationException::withMessages(['variant' => 'Invalid product variant.']);
        }
    }

    protected function assertStockAvailable(Product $product, ?ProductVariant $variant, int $quantity): void
    {
        $available = $variant ? $variant->stock_quantity : $product->stock_quantity;

        if ($quantity > $available) {
            throw ValidationException::withMessages([
                'quantity' => "Only {$available} unit(s) of \"{$product->name}\" left in stock.",
            ]);
        }
    }
}
