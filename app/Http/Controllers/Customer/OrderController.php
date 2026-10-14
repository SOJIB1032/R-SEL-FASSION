<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use App\Notifications\ShopOrderNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService)
    {
    }

    public function index(Request $request): View
    {
        $orders = $request->user()->orders()->with('items')->latest()->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    public function show(Request $request, Order $order): View
    {
        abort_unless($order->user_id === $request->user()->id, 403);
        $order->load('items.product.primaryImage', 'items.review', 'payments');

        return view('customer.orders.show', compact('order'));
    }

    public function track(Request $request, Order $order): View
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $steps = ['pending', 'confirmed', 'processing', 'shipped', 'delivered'];
        $currentIndex = array_search($order->status, $steps, true);

        return view('customer.orders.track', compact('order', 'steps', 'currentIndex'));
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        try {
            $this->orderService->cancel($order, $request->user()->id);
            $request->user()->notify(new ShopOrderNotification($order, 'Order cancelled'));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return back()->with('status', 'Your order has been cancelled and stock has been restored.');
    }
}
