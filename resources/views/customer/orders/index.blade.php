@extends('layouts.app')
@section('title', 'My Orders - R-SELFASSION')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    @include('customer.partials.account-nav')

    <h1 class="text-2xl font-bold mb-6">My Orders</h1>

    @if (session('status'))
        <div class="bg-green-50 text-green-700 text-sm rounded-lg p-3 mb-4">{{ session('status') }}</div>
    @endif

    @if($orders->isEmpty())
        <div class="bg-white rounded-xl shadow-sm p-12 text-center text-gray-500">
            <p class="text-lg font-medium mb-2">You haven't placed any orders yet</p>
            <a href="{{ route('products.index') }}" class="text-brand font-medium hover:underline">Start shopping →</a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
                <a href="{{ route('account.orders.show', $order) }}" class="block bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <p class="font-semibold">{{ $order->order_number }}</p>
                            <p class="text-xs text-gray-500">{{ $order->created_at->format('M d, Y') }} &bull; {{ $order->items->count() }} item(s)</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full
                                @class([
                                    'bg-yellow-100 text-yellow-700' => $order->status === 'pending',
                                    'bg-blue-100 text-blue-700' => in_array($order->status, ['confirmed', 'processing', 'shipped']),
                                    'bg-green-100 text-green-700' => $order->status === 'delivered',
                                    'bg-red-100 text-red-700' => in_array($order->status, ['cancelled', 'returned']),
                                ])">
                                {{ ucfirst($order->status) }}
                            </span>
                            <span class="font-bold text-brand">৳{{ number_format($order->grand_total, 2) }}</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-6">{{ $orders->links() }}</div>
    @endif
</div>
@endsection
