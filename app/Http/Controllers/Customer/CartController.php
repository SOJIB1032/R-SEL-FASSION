<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use App\Services\CouponService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(protected CartService $cartService, protected CouponService $couponService)
    {
    }

    public function show(Request $request): View
    {
        $cart = $this->cartService->currentCart($request)->load('items.product.primaryImage', 'items.variant');

        return view('customer.cart.show', compact('cart'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'product_variant_id' => ['nullable', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:999'],
        ]);

        $product = Product::findOrFail($data['product_id']);
        $variant = isset($data['product_variant_id']) ? ProductVariant::find($data['product_variant_id']) : null;

        try {
            $cart = $this->cartService->currentCart($request);
            $this->cartService->addItem($cart, $product, $variant, (int) $data['quantity']);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        if ($request->input('action') === 'buy_now') {
            return redirect()->route('checkout.show')->with('status', 'Sign in to complete your purchase.');
        }

        return back()->with('status', "\"{$product->name}\" was added to your cart.");
    }

    public function update(Request $request, CartItem $cartItem): RedirectResponse
    {
        $this->authorizeItem($request, $cartItem);

        $data = $request->validate(['quantity' => ['required', 'integer', 'min:0', 'max:999']]);

        try {
            $this->cartService->updateItemQuantity($cartItem, (int) $data['quantity']);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        if ($request->expectsJson()) {
            $cart = $this->cartService->currentCart($request)->load('items');
            return response()->json([
                'message' => 'Cart updated.',
                'subtotal' => $cart->subtotal,
                'discount' => (float) $cart->coupon_discount,
                'total' => $cart->grand_total,
            ]);
        }

        return back()->with('status', 'Cart updated.');
    }

    public function destroy(Request $request, CartItem $cartItem): RedirectResponse
    {
        $this->authorizeItem($request, $cartItem);
        $this->cartService->removeItem($cartItem);

        if ($request->expectsJson()) {
            $cart = $this->cartService->currentCart($request)->load('items');
            return response()->json([
                'message' => 'Item removed from cart.',
                'subtotal' => $cart->subtotal,
                'discount' => (float) $cart->coupon_discount,
                'total' => $cart->grand_total,
            ]);
        }

        return back()->with('status', 'Item removed from cart.');
    }

    public function applyCoupon(Request $request): RedirectResponse
    {
        $request->validate(['code' => ['required', 'string']]);

        $cart = $this->cartService->currentCart($request)->load('items');

        if (! $request->user()) {
            return back()->withErrors(['coupon' => 'Please log in to apply a coupon code.']);
        }

        try {
            ['discount' => $discount] = $this->couponService->validateAndCalculate($request->string('code'), $cart->subtotal, $request->user());
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        $cart->update(['coupon_code' => strtoupper($request->string('code')), 'coupon_discount' => $discount]);

        return back()->with('status', 'Coupon applied.');
    }

    public function removeCoupon(Request $request): RedirectResponse
    {
        $cart = $this->cartService->currentCart($request);
        $cart->update(['coupon_code' => null, 'coupon_discount' => 0]);

        return back()->with('status', 'Coupon removed.');
    }

    protected function authorizeItem(Request $request, CartItem $cartItem): void
    {
        $cart = $this->cartService->currentCart($request);
        abort_unless($cartItem->cart_id === $cart->id, 403);
    }
}
