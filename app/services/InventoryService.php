<?php

namespace App\Services;

use App\Models\InventoryTransaction;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;

class InventoryService
{
    /**
     * Reduce stock after a successful order and log the transaction.
     */
    public function decrease(Product $product, ?ProductVariant $variant, int $quantity, ?Order $order = null, ?int $userId = null): void
    {
        if ($variant) {
            $variant->decrement('stock_quantity', $quantity);
        }
        $product->decrement('stock_quantity', $quantity);

        InventoryTransaction::create([
            'product_id' => $product->id,
            'product_variant_id' => $variant?->id,
            'type' => 'decrease',
            'quantity' => $quantity,
            'reason' => $order ? 'Order #'.$order->order_number : 'Manual adjustment',
            'order_id' => $order?->id,
            'created_by' => $userId,
        ]);
    }

    /**
     * Restore stock after a cancellation or refund.
     */
    public function increase(Product $product, ?ProductVariant $variant, int $quantity, ?Order $order = null, ?int $userId = null, string $reason = 'Order cancelled/refunded'): void
    {
        if ($variant) {
            $variant->increment('stock_quantity', $quantity);
        }
        $product->increment('stock_quantity', $quantity);

        InventoryTransaction::create([
            'product_id' => $product->id,
            'product_variant_id' => $variant?->id,
            'type' => 'increase',
            'quantity' => $quantity,
            'reason' => $order ? $reason.' — Order #'.$order->order_number : $reason,
            'order_id' => $order?->id,
            'created_by' => $userId,
        ]);
    }

    public function restoreStockForOrder(Order $order, ?int $userId = null, string $reason = 'Order cancelled/refunded'): void
    {
        foreach ($order->items as $item) {
            $this->increase($item->product, $item->variant, $item->quantity, $order, $userId, $reason);
        }
    }
}
