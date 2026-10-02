@extends('layouts.app')
@section('title', 'Order '.$order->order_number.' - R-SELFASSION')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    @include('customer.partials.account-nav')

    @if (session('status'))
        <div class="bg-green-50 text-green-700 text-sm rounded-lg p-3 mb-4">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="bg-red-50 text-red-700 text-sm rounded-lg p-3 mb-4">
            @foreach ($errors->all() as $error) <p>{{ $error }}</p> @endforeach
        </div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Order {{ $order->order_number }}</h1>
            <p class="text-sm text-gray-500">Placed on {{ $order->created_at->format('M d, Y \a\t g:i A') }}</p>
        </div>
        <a href="{{ route('account.orders.track', $order) }}" class="text-brand text-sm font-medium hover:underline">Track Order →</a>
    </div>

    <div class="grid md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-4">
            <p class="text-xs text-gray-500 mb-1">Order Status</p>
            <p class="font-semibold">{{ ucfirst($order->status) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4">
            <p class="text-xs text-gray-500 mb-1">Payment</p>
            <p class="font-semibold">{{ strtoupper($order->payment_method) }} &bull; {{ ucfirst($order->payment_status) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4">
            <p class="text-xs text-gray-500 mb-1">Shipping To</p>
            <p class="font-semibold text-sm">{{ $order->address_line }}, {{ $order->city }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h2 class="font-semibold mb-4">Items</h2>
        <div class="space-y-4">
            @foreach($order->items as $item)
                <div class="flex items-center gap-4 border-b last:border-0 pb-4 last:pb-0">
                    <div class="w-16 h-16 bg-gray-100 rounded-lg overflow-hidden shrink-0">
                        @if($item->product?->primaryImage)
                            <img src="{{ asset('storage/'.$item->product->primaryImage->path) }}" class="object-cover w-full h-full">
                        @endif
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-sm">{{ $item->product_name }}</p>
                        @if($item->variant_label)
                            <p class="text-xs text-gray-500">{{ $item->variant_label }}</p>
                        @endif
                        <p class="text-xs text-gray-500">Qty: {{ $item->quantity }} × ৳{{ number_format($item->unit_price, 2) }}</p>
                    </div>
                    <p class="font-semibold">৳{{ number_format($item->line_total, 2) }}</p>
                </div>
            @endforeach
        </div>

        <div class="space-y-2 text-sm border-t pt-4 mt-4">
            <div class="flex justify-between"><span>Subtotal</span><span>৳{{ number_format($order->subtotal, 2) }}</span></div>
            @if($order->discount_total > 0)
                <div class="flex justify-between text-green-600"><span>Discount ({{ $order->coupon_code }})</span><span>-৳{{ number_format($order->discount_total, 2) }}</span></div>
            @endif
            <div class="flex justify-between"><span>Shipping</span><span>৳{{ number_format($order->shipping_fee, 2) }}</span></div>
            @if($order->tax_total > 0)
                <div class="flex justify-between"><span>Tax</span><span>৳{{ number_format($order->tax_total, 2) }}</span></div>
            @endif
            <div class="flex justify-between font-bold text-base border-t pt-2"><span>Total</span><span>৳{{ number_format($order->grand_total, 2) }}</span></div>
        </div>
    </div>

    @if(! in_array($order->status, ['delivered', 'cancelled', 'returned', 'shipped']))
        <form action="{{ route('account.orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Cancel this order?');">
            @csrf
            <button class="border border-red-300 text-red-600 rounded-lg px-5 py-2 text-sm font-semibold hover:bg-red-50">Cancel Order</button>
        </form>
    @endif
</div>
@endsection
