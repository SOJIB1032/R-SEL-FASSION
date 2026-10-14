<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReviewController extends Controller
{
    public function store(Request $request, Order $order, OrderItem $orderItem): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403);
        abort_unless($orderItem->order_id === $order->id, 404);
        abort_unless($order->status === 'delivered', 403, 'Reviews are available after delivery.');

        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'title' => ['nullable', 'string', 'max:120'],
            'description' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        Review::updateOrCreate(
            ['user_id' => $request->user()->id, 'order_item_id' => $orderItem->id],
            [
                'product_id' => $orderItem->product_id,
                'rating' => $data['rating'],
                'title' => $data['title'] ?? null,
                'description' => $data['description'],
                'status' => 'pending',
            ]
        );

        return back()->with('status', 'Review submitted. It will appear after admin moderation.');
    }
}
