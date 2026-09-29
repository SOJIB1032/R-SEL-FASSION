<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(
        protected InventoryService $inventoryService,
        protected CouponService $couponService,
    ) {
    }

    /**
     * Create an order from the given cart. All prices, stock, and the coupon
     * discount are re-validated and re-computed here — server-side — instead
     * of trusting anything posted from the browser.
     */
    public function createFromCart(Cart $cart, User $user, array $shipping): Order
    {
        $cart->load('items.product', 'items.variant');

        if ($cart->items->isEmpty()) {
            throw ValidationException::withMessages(['cart' => 'Your cart is empty.']);
        }

        foreach ($cart->items as $item) {
            $available = $item->variant ? $item->variant->stock_quantity : $item->product->stock_quantity;
            if ($item->quantity > $available) {
                throw ValidationException::withMessages([
                    'cart' => "\"{$item->product->name}\" only has {$available} unit(s) left in stock.",
                ]);
            }
            if ($item->product->status !== 'active') {
                throw ValidationException::withMessages(['cart' => "\"{$item->product->name}\" is no longer available."]);
            }
        }

        // Recompute prices from the database — never trust cart_item.unit_price alone at checkout time.
        $subtotal = $cart->items->sum(function ($item) {
            $livePrice = $item->product->final_price + ($item->variant?->price_adjustment ?? 0);

            return $livePrice * $item->quantity;
        });

        $discountTotal = 0;
        $coupon = null;

        if ($cart->coupon_code) {
            try {
                ['coupon' => $coupon, 'discount' => $discountTotal] = $this->couponService->validateAndCalculate($cart->coupon_code, $subtotal, $user);
            } catch (ValidationException $e) {
                // Coupon became invalid between cart and checkout (expired, limit hit) — drop it silently, don't block checkout.
                $coupon = null;
                $discountTotal = 0;
            }
        }

        $shippingFee = (float) config('shopnova.flat_shipping_fee', 60);
        $taxPercent = (float) config('shopnova.tax_percent', 0);
        $taxTotal = round(($subtotal - $discountTotal) * ($taxPercent / 100), 2);
        $grandTotal = max(0, $subtotal - $discountTotal + $shippingFee + $taxTotal);

        return DB::transaction(function () use ($cart, $user, $shipping, $subtotal, $discountTotal, $shippingFee, $taxTotal, $grandTotal, $coupon) {
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'user_id' => $user->id,
                'full_name' => $shipping['full_name'],
                'email' => $shipping['email'],
                'phone' => $shipping['phone'],
                'address_line' => $shipping['address_line'],
                'city' => $shipping['city'],
                'area' => $shipping['area'] ?? null,
                'postal_code' => $shipping['postal_code'] ?? null,
                'shipping_method' => $shipping['shipping_method'] ?? 'standard',
                'payment_method' => $shipping['payment_method'],
                'order_notes' => $shipping['order_notes'] ?? null,
                'subtotal' => $subtotal,
                'discount_total' => $discountTotal,
                'shipping_fee' => $shippingFee,
                'tax_total' => $taxTotal,
                'grand_total' => $grandTotal,
                'coupon_code' => $coupon?->code,
                'status' => 'pending',
                'payment_status' => 'pending',
            ]);

            foreach ($cart->items as $item) {
                $livePrice = $item->product->final_price + ($item->variant?->price_adjustment ?? 0);

                $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'product_name' => $item->product->name,
                    'variant_label' => $item->variant?->label,
                    'quantity' => $item->quantity,
                    'unit_price' => $livePrice,
                    'line_total' => $livePrice * $item->quantity,
                ]);

                $this->inventoryService->decrease($item->product, $item->variant, $item->quantity, $order, $user->id);
                $item->product->increment('sales_count', $item->quantity);
            }

            Payment::create([
                'order_id' => $order->id,
                'method' => $shipping['payment_method'],
                'amount' => $grandTotal,
                'status' => $shipping['payment_method'] === 'cod' ? 'pending' : 'pending',
            ]);

            if ($coupon) {
                $this->couponService->recordUsage($coupon, $user, $order, $discountTotal);
            }

            $cart->items()->delete();
            $cart->update(['coupon_code' => null, 'coupon_discount' => 0]);

            return $order;
        });
    }

    public function cancel(Order $order, ?int $byUserId = null): void
    {
        if (in_array($order->status, ['delivered', 'cancelled', 'returned'], true)) {
            throw ValidationException::withMessages(['order' => 'This order can no longer be cancelled.']);
        }

        $order->update(['status' => 'cancelled', 'cancelled_at' => now()]);
        $this->inventoryService->restoreStockForOrder($order, $byUserId, 'Order cancelled');

        if ($order->payment_status === 'paid') {
            $order->update(['payment_status' => 'refunded']);
        }
    }

    protected function generateOrderNumber(): string
    {
        do {
            $number = 'SN-'.now()->format('Ymd').'-'.strtoupper(Str::random(6));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }
}
