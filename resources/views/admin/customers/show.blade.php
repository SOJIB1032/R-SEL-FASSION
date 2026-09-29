@extends('layouts.admin')
@section('title', $customer->name.' - ShopNova Admin')
@section('page_title', 'Customer: '.$customer->name)

@section('content')
<div class="grid md:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="font-semibold mb-3">Profile</h2>
        <p class="text-sm"><strong>Name:</strong> {{ $customer->name }}</p>
        <p class="text-sm"><strong>Email:</strong> {{ $customer->email }}</p>
        <p class="text-sm"><strong>Phone:</strong> {{ $customer->phone ?? '—' }}</p>
        <p class="text-sm"><strong>Joined:</strong> {{ $customer->created_at->format('M d, Y') }}</p>
        <form action="{{ route('admin.customers.toggle-active', $customer) }}" method="POST" class="mt-4">
            @csrf
            <button class="w-full rounded-lg py-2 text-sm font-semibold {{ $customer->is_active ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-green-50 text-green-600 hover:bg-green-100' }}">
                {{ $customer->is_active ? 'Deactivate Account' : 'Activate Account' }}
            </button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="font-semibold mb-3">Saved Addresses</h2>
        @forelse($customer->addresses as $address)
            <div class="text-sm border-b last:border-0 py-2">
                <p class="font-medium">{{ $address->full_name }} @if($address->is_default)<span class="text-xs text-brand">(default)</span>@endif</p>
                <p class="text-gray-500">{{ $address->address_line }}, {{ $address->city }}</p>
            </div>
        @empty
            <p class="text-sm text-gray-500">No saved addresses.</p>
        @endforelse
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="font-semibold mb-3">Order Summary</h2>
        <p class="text-sm">Total orders: <strong>{{ $customer->orders->count() }}</strong></p>
        <p class="text-sm">Total spent: <strong>৳{{ number_format($customer->orders->sum('grand_total'), 2) }}</strong></p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm mt-6 overflow-hidden">
    <div class="px-4 py-3 font-semibold border-b">Recent Orders</div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-500">
            <tr><th class="px-4 py-3">Order</th><th class="px-4 py-3">Date</th><th class="px-4 py-3">Total</th><th class="px-4 py-3">Status</th><th class="px-4 py-3 text-right">Action</th></tr>
        </thead>
        <tbody class="divide-y">
            @forelse($customer->orders as $order)
                <tr>
                    <td class="px-4 py-3 font-medium">{{ $order->order_number }}</td>
                    <td class="px-4 py-3">{{ $order->created_at->format('M d, Y') }}</td>
                    <td class="px-4 py-3">৳{{ number_format($order->grand_total, 2) }}</td>
                    <td class="px-4 py-3 capitalize">{{ $order->status }}</td>
                    <td class="px-4 py-3 text-right"><a href="{{ route('admin.orders.show', $order) }}" class="text-brand hover:underline">View</a></td>
                </tr>
            @empty
                <tr><td class="px-4 py-6 text-center text-gray-500" colspan="5">No orders yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
