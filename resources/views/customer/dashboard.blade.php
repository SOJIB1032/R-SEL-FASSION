@extends('layouts.app')
@section('title', 'My Account - R-SELFASSION')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    @include('customer.partials.account-nav')

    <h1 class="text-2xl font-bold mb-6">Welcome back, {{ auth()->user()->name }}</h1>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-xs text-gray-500 mb-1">Total Orders</p>
            <p class="text-2xl font-bold">{{ $stats['total_orders'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-xs text-gray-500 mb-1">Pending Orders</p>
            <p class="text-2xl font-bold">{{ $stats['pending_orders'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-xs text-gray-500 mb-1">Completed Orders</p>
            <p class="text-2xl font-bold">{{ $stats['completed_orders'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-xs text-gray-500 mb-1">Wishlist Items</p>
            <p class="text-2xl font-bold">{{ $stats['wishlist_items'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold">Recent Orders</h2>
            <a href="{{ route('account.orders.index') }}" class="text-brand text-sm font-medium hover:underline">View all</a>
        </div>

        @forelse($recentOrders as $order)
            <a href="{{ route('account.orders.show', $order) }}" class="flex items-center justify-between py-3 border-b last:border-0 hover:text-brand">
                <div>
                    <p class="font-medium text-sm">{{ $order->order_number }}</p>
                    <p class="text-xs text-gray-500">{{ $order->created_at->format('M d, Y') }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs capitalize">{{ $order->status }}</p>
                    <p class="font-semibold text-sm">৳{{ number_format($order->grand_total, 2) }}</p>
                </div>
            </a>
        @empty
            <p class="text-sm text-gray-500">No orders yet.</p>
        @endforelse
    </div>
</div>
@endsection
