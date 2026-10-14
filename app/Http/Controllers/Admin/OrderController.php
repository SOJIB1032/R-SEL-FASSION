<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\InventoryService;
use App\Services\OrderService;
use App\Notifications\ShopOrderNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService, protected InventoryService $inventoryService)
    {
    }

    public function index(Request $request): View
    {
        $query = Order::query()->with('user');

        if ($search = $request->string('q')->trim()->value()) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->string('payment_status'));
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->date('to'));
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load('items.product', 'user', 'payments');

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:'.implode(',', Order::STATUSES)]]);

        $timestampField = match ($data['status']) {
            'confirmed' => 'confirmed_at',
            'shipped' => 'shipped_at',
            'delivered' => 'delivered_at',
            'cancelled' => 'cancelled_at',
            default => null,
        };

        $updates = ['status' => $data['status']];
        if ($timestampField && ! $order->{$timestampField}) {
            $updates[$timestampField] = now();
        }

        if ($data['status'] === 'cancelled') {
            try {
                $this->orderService->cancel($order, auth()->id());
            } catch (ValidationException $e) {
                return back()->withErrors($e->errors());
            }
            $order->refresh();
            $order->user?->notify(new ShopOrderNotification($order, 'Order cancelled'));

            return back()->with('status', 'Order cancelled and stock restored.');
        }

        if ($data['status'] === 'delivered' && $order->payment_method === 'cod') {
            $updates['payment_status'] = 'paid';
        }

        $order->update($updates);
        $order->user?->notify(new ShopOrderNotification($order, 'Order '.ucfirst($order->status)));

        return back()->with('status', 'Order status updated.');
    }

    public function updatePaymentStatus(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate(['payment_status' => ['required', 'in:'.implode(',', Order::PAYMENT_STATUSES)]]);
        $order->update($data);

        return back()->with('status', 'Payment status updated.');
    }

    public function refund(Order $order): RedirectResponse
    {
        if ($order->payment_status !== 'paid') {
            return back()->withErrors(['order' => 'Only paid orders can be refunded.']);
        }

        $order->update(['payment_status' => 'refunded']);
        if ($order->status !== 'returned') {
            $this->inventoryService->restoreStockForOrder($order, auth()->id(), 'Order refunded by admin');
            $order->update(['status' => 'returned']);
        }

        return back()->with('status', 'Order refunded and stock restored.');
    }

    public function invoice(Order $order): View
    {
        $order->load('items', 'user');

        return view('admin.orders.invoice', compact('order'));
    }
}
