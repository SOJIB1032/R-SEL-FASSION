@extends('layouts.app')
@section('title', 'Order Confirmed - R-SELFASSION')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-16 text-center">
    <div class="text-5xl mb-4">✅</div>
    <h1 class="text-2xl font-bold mb-2">Thank you, {{ $order->full_name }}!</h1>
    <p class="text-gray-600 mb-6">Your order <strong>{{ $order->order_number }}</strong> has been placed successfully.</p>

    <div class="bg-white rounded-xl shadow-sm p-6 text-left">
        <div class="flex justify-between text-sm mb-4">
            <span class="text-gray-500">Payment Method</span>
            <span class="font-medium">{{ strtoupper($order->payment_method) }} — Pay on delivery</span>
        </div>
        <div class="space-y-2 text-sm border-t pt-4">
            @foreach($order->items as $item)
                <div class="flex justify-between">
                    <span>{{ $item->product_name }} × {{ $item->quantity }}</span>
                    <span>৳{{ number_format($item->line_total, 2) }}</span>
                </div>
            @endforeach
        </div>
        <div class="space-y-2 text-sm border-t pt-4 mt-4">
            <div class="flex justify-between"><span>Subtotal</span><span>৳{{ number_format($order->subtotal, 2) }}</span></div>
            @if($order->discount_total > 0)
                <div class="flex justify-between text-green-600"><span>Discount</span><span>-৳{{ number_format($order->discount_total, 2) }}</span></div>
            @endif
            <div class="flex justify-between"><span>Shipping</span><span>৳{{ number_format($order->shipping_fee, 2) }}</span></div>
            @if($order->tax_total > 0)
                <div class="flex justify-between"><span>Tax</span><span>৳{{ number_format($order->tax_total, 2) }}</span></div>
            @endif
            <div class="flex justify-between font-bold text-base border-t pt-2"><span>Total</span><span>৳{{ number_format($order->grand_total, 2) }}</span></div>
        </div>
    </div>

    <div class="flex justify-center gap-4 mt-8">
        <a href="{{ route('account.orders.show', $order) }}" class="bg-brand text-white rounded-lg px-6 py-2.5 font-semibold hover:bg-brand-dark">View Order</a>
        <a href="{{ route('products.index') }}" class="border rounded-lg px-6 py-2.5 font-semibold hover:bg-gray-50">Continue Shopping</a>
    </div>
</div>
@endsection
