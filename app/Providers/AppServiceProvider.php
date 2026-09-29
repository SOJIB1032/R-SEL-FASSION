<?php

namespace App\Providers;

use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('partials.navbar', function ($view) {
            $cartCount = 0;
            $wishlistCount = 0;

            try {
                $cart = app(CartService::class)->currentCart(request());
                $cartCount = $cart->items()->sum('quantity');

                if (Auth::check()) {
                    $wishlistCount = optional(Auth::user()->wishlist)->items()->count() ?? 0;
                }
            } catch (\Throwable $e) {
                // Storefront should never break because of a cart/wishlist count glitch.
            }

            $view->with(compact('cartCount', 'wishlistCount'));
        });
    }
}
