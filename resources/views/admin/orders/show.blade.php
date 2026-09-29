@extends('layouts.admin')
@section('title', 'Order '.$order->order_number.' - ShopNova Admin')
@section('page_title', 'Order '.$order->order_number)

@section('content')
<div class="flex justify-end gap-2 mb-4">
    <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank" class="border rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-50">🖨️ Print Invoice</a>
</div>

<div class="grid md:grid-cols-3 gap-6">
    <div class="md:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="font-semibold mb-4">Items</h2>
            <div class="space-y-3">
                @foreach($order->items as $item)
                    <div class="flex justify-between text-sm border-b last:border-0 pb-3 last:pb-0">
                        <div>
                            <p class="font-medium">{{ $item->product_name }}</p>
                            @if($item->variant_label)<p class="text-xs text-gray-500">{{ $item->variant_label }}</p>@endif
                            <p class="text-xs text-gray-500">Qty {{ $item->quantity }} × ৳{{ number_format($item->unit_price, 2) }}</p>
                        </div>
                        <p class="font-semibold">৳{{ number_format($item->line_total, 2) }}</p>
                    </div>
                @endforeach
            </div>
            <div class="space-y-1 text-sm border-t pt-4 mt-4">
                <div class="flex justify-between"><span>Subtotal</span><span>৳{{ number_format($order->subtotal, 2) }}</span></div>
                @if($order->discount_total > 0)<div class="flex justify-between text-green-600"><span>Discount</span><span>-৳{{ number_format($order->discount_total, 2) }}</span></div>@endif
                <div class="flex justify-between"><span>Shipping</span><span>৳{{ number_format($order->shipping_fee, 2) }}</span></div>
                @if($order->tax_total > 0)<div class="flex justify-between"><span>Tax</span><span>৳{{ number_format($order->tax_total, 2) }}</span></div>@endif
                <div class="flex justify-between font-bold text-base border-t pt-2"><span>Total</span><span>৳{{ number_format($order->grand_total, 2) }}</span></div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="font-semibold mb-4">Customer &amp; Shipping</h2>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500 text-xs mb-1">Customer</p>
                    <p>{{ $order->full_name }}</p>
                    <p>{{ $order->email }}</p>
                    <p>{{ $order->phone }}</p>
                    @if($order->user)
                        <a href="{{ route('admin.customers.show', $order->user) }}" class="text-brand text-xs hover:underline">View customer profile →</a>
                    @endif
                </div>
                <div>
                    <p class="text-gray-500 text-xs mb-1">Ship To</p>
                    <p>{{ $order->address_line }}</p>
                    <p>{{ $order->city }} {{ $order->area }} {{ $order->postal_code }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ ucfirst($order->shipping_method) }} shipping</p>
                </div>
            </div>
            @if($order->order_notes)
                <div class="mt-4 text-sm">
                    <p class="text-gray-500 text-xs mb-1">Order Notes</p>
                    <p>{{ $order->order_notes }}</p>
                </div>
            @endif
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="font-semibold mb-3">Order Status</h2>
            <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="flex gap-2">
                @csrf
                <select name="status" class="flex-1 border rounded-lg px-3 py-2 text-sm">
                    @foreach(\App\Models\Order::STATUSES as $status)
                        <option value="{{ $status }}" @selected($order->status === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <button class="bg-brand text-white rounded-lg px-4 text-sm font-semibold">Update</button>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="font-semibold mb-3">Payment Status</h2>
            <p class="text-xs text-gray-500 mb-2">Method: {{ strtoupper($order->payment_method) }}</p>
            <form action="{{ route('admin.orders.update-payment-status', $order) }}" method="POST" class="flex gap-2">
                @csrf
                <select name="payment_status" class="flex-1 border rounded-lg px-3 py-2 text-sm">
                    @foreach(\App\Models\Order::PAYMENT_STATUSES as $status)
                        <option value="{{ $status }}" @selected($order->payment_status === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <button class="bg-brand text-white rounded-lg px-4 text-sm font-semibold">Update</button>
            </form>

            @if($order->payment_status === 'paid')
                <form action="{{ route('admin.orders.refund', $order) }}" method="POST" class="mt-3" onsubmit="return confirm('Refund this order and restore stock?');">
                    @csrf
                    <button class="w-full border border-red-300 text-red-600 rounded-lg py-2 text-sm font-semibold hover:bg-red-50">Process Refund</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
