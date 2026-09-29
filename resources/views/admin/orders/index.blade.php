@extends('layouts.admin')
@section('title', 'Orders - ShopNova Admin')
@section('page_title', 'Orders')

@section('content')
<form method="GET" class="flex flex-wrap gap-2 mb-4">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Order #, name, email, phone..." class="border rounded-lg px-3 py-2 text-sm">
    <select name="status" class="border rounded-lg px-2 py-2 text-sm" onchange="this.form.submit()">
        <option value="">Any Status</option>
        @foreach(\App\Models\Order::STATUSES as $status)
            <option value="{{ $status }}" @selected(request('status') == $status)>{{ ucfirst($status) }}</option>
        @endforeach
    </select>
    <select name="payment_status" class="border rounded-lg px-2 py-2 text-sm" onchange="this.form.submit()">
        <option value="">Any Payment Status</option>
        @foreach(\App\Models\Order::PAYMENT_STATUSES as $status)
            <option value="{{ $status }}" @selected(request('payment_status') == $status)>{{ ucfirst($status) }}</option>
        @endforeach
    </select>
    <input type="date" name="from" value="{{ request('from') }}" class="border rounded-lg px-2 py-2 text-sm">
    <input type="date" name="to" value="{{ request('to') }}" class="border rounded-lg px-2 py-2 text-sm">
    <button class="bg-gray-800 text-white rounded-lg px-4 text-sm">Filter</button>
</form>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-500">
            <tr>
                <th class="px-4 py-3">Order</th>
                <th class="px-4 py-3">Customer</th>
                <th class="px-4 py-3">Date</th>
                <th class="px-4 py-3">Total</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Payment</th>
                <th class="px-4 py-3 text-right">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @foreach($orders as $order)
                <tr>
                    <td class="px-4 py-3 font-medium">{{ $order->order_number }}</td>
                    <td class="px-4 py-3">{{ $order->full_name }}<br><span class="text-xs text-gray-400">{{ $order->email }}</span></td>
                    <td class="px-4 py-3">{{ $order->created_at->format('M d, Y') }}</td>
                    <td class="px-4 py-3 font-semibold">৳{{ number_format($order->grand_total, 2) }}</td>
                    <td class="px-4 py-3">
                        <span class="text-xs font-semibold px-2 py-1 rounded-full
                            @class([
                                'bg-yellow-100 text-yellow-700' => $order->status === 'pending',
                                'bg-blue-100 text-blue-700' => in_array($order->status, ['confirmed', 'processing', 'shipped']),
                                'bg-green-100 text-green-700' => $order->status === 'delivered',
                                'bg-red-100 text-red-700' => in_array($order->status, ['cancelled', 'returned']),
                            ])">{{ ucfirst($order->status) }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="text-xs font-semibold px-2 py-1 rounded-full {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-brand font-medium hover:underline">View</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $orders->links() }}</div>
@endsection
