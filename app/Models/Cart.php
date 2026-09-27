<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['user_id', 'session_token', 'coupon_code', 'coupon_discount'];

    protected function casts(): array
    {
        return ['coupon_discount' => 'decimal:2'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function getSubtotalAttribute(): float
    {
        return (float) $this->items->sum(fn ($item) => $item->unit_price * $item->quantity);
    }

    public function getGrandTotalAttribute(): float
    {
        return max(0, $this->subtotal - (float) $this->coupon_discount);
    }
}
