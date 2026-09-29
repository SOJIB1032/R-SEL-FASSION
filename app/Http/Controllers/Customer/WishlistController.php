<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function __construct(protected CartService $cartService)
    {
    }

    public function show(): View
    {
        $wishlist = Auth::user()->wishlist()->with('items.product.primaryImage')->firstOrCreate([]);

        return view('customer.wishlist.show', compact('wishlist'));
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        $wishlist = Auth::user()->wishlist()->firstOrCreate([]);

        $wishlist->items()->firstOrCreate(['product_id' => $product->id]);

        return back()->with('status', "\"{$product->name}\" was added to your wishlist.");
    }

    public function destroy(Product $product): RedirectResponse
    {
        $wishlist = Auth::user()->wishlist;

        $wishlist?->items()->where('product_id', $product->id)->delete();

        return back()->with('status', 'Item removed from wishlist.');
    }

    public function moveToCart(Request $request, Product $product): RedirectResponse
    {
        $wishlist = Auth::user()->wishlist;

        try {
            $cart = $this->cartService->currentCart($request);
            $this->cartService->addItem($cart, $product, null, 1);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        $wishlist?->items()->where('product_id', $product->id)->delete();

        return back()->with('status', "\"{$product->name}\" moved to your cart.");
    }
}
