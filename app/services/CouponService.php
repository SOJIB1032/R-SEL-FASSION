<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class CouponService
{
    /**
     * Validate a coupon code against the current subtotal and user history.
     * Returns the discount amount. All checks happen server-side — never
     * trust a discount amount posted from the browser.
     */
    public function validateAndCalculate(string $code, float $subtotal, User $user): array
    {
        $coupon = Coupon::whereRaw('UPPER(code) = ?', [strtoupper(trim($code))])->first();

        if (! $coupon || ! $coupon->isCurrentlyValid()) {
            throw ValidationException::withMessages(['coupon' => 'This coupon code is invalid or has expired.']);
        }

        if ($subtotal < $coupon->min_order_amount) {
            throw ValidationException::withMessages([
                'coupon' => "This coupon requires a minimum order of ৳".number_format($coupon->min_order_amount, 2).".",
            ]);
        }

        if ($coupon->per_user_limit !== null) {
            $usedByUser = CouponUsage::where('coupon_id', $coupon->id)->where('user_id', $user->id)->count();
            if ($usedByUser >= $coupon->per_user_limit) {
                throw ValidationException::withMessages(['coupon' => 'You have already used this coupon.']);
            }
        }

        $discount = $coupon->type === 'percentage'
            ? round($subtotal * ((float) $coupon->value / 100), 2)
            : (float) $coupon->value;

        if ($coupon->max_discount_amount !== null) {
            $discount = min($discount, (float) $coupon->max_discount_amount);
        }

        $discount = min($discount, $subtotal);

        return ['coupon' => $coupon, 'discount' => $discount];
    }

    public function recordUsage(Coupon $coupon, User $user, Order $order, float $discountAmount): void
    {
        CouponUsage::create([
            'coupon_id' => $coupon->id,
            'user_id' => $user->id,
            'order_id' => $order->id,
            'discount_amount' => $discountAmount,
        ]);

        $coupon->increment('times_used');
    }
}
