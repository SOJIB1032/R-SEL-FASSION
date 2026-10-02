@extends('layouts.admin')
@section('title', 'Reports - R-SELFASSION Admin')
@section('page_title', 'Sales Reports')

@section('content')
<form method="GET" class="flex flex-wrap gap-2 mb-6">
    <select name="period" class="border rounded-lg px-3 py-2 text-sm" onchange="this.form.submit()">
        <option value="daily" @selected($period === 'daily')>Daily</option>
        <option value="weekly" @selected($period === 'weekly')>Weekly</option>
        <option value="monthly" @selected($period === 'monthly')>Monthly</option>
        <option value="yearly" @selected($period === 'yearly')>Yearly</option>
    </select>
    <input type="date" name="from" value="{{ request('from', $from->format('Y-m-d')) }}" class="border rounded-lg px-3 py-2 text-sm">
    <input type="date" name="to" value="{{ request('to', $to->format('Y-m-d')) }}" class="border rounded-lg px-3 py-2 text-sm">
    <button class="bg-gray-800 text-white rounded-lg px-4 text-sm">Apply</button>
</form>

<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-5">
        <p class="text-xs text-gray-500 mb-1">Total Revenue (Paid)</p>
        <p class="text-2xl font-bold text-brand">৳{{ number_format($summary['total_revenue'], 2) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5">
        <p class="text-xs text-gray-500 mb-1">Total Orders</p>
        <p class="text-2xl font-bold">{{ $summary['total_orders'] }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5">
        <p class="text-xs text-gray-500 mb-1">Average Order Value</p>
        <p class="text-2xl font-bold">৳{{ number_format($summary['average_order_value'], 2) }}</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm p-5 mb-6">
    <h2 class="font-semibold mb-3 text-sm">Sales Trend ({{ ucfirst($period) }})</h2>
    <canvas id="salesChart" height="90"></canvas>
</div>

<div class="grid md:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl shadow-sm p-5">
        <h2 class="font-semibold mb-3 text-sm">Best-Selling Products</h2>
        <table class="w-full text-xs">
            <thead class="text-gray-500 text-left"><tr><th class="pb-2">Product</th><th class="pb-2">Units</th><th class="pb-2 text-right">Revenue</th></tr></thead>
            <tbody class="divide-y">
                @forelse($bestSellingProducts as $p)
                    <tr><td class="py-1.5">{{ $p->product_name }}</td><td class="py-1.5">{{ $p->units }}</td><td class="py-1.5 text-right">৳{{ number_format($p->revenue, 2) }}</td></tr>
                @empty
                    <tr><td colspan="3" class="py-4 text-center text-gray-400">No data in this range.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-5">
        <h2 class="font-semibold mb-3 text-sm">Best-Selling Categories</h2>
        <table class="w-full text-xs">
            <thead class="text-gray-500 text-left"><tr><th class="pb-2">Category</th><th class="pb-2 text-right">Revenue</th></tr></thead>
            <tbody class="divide-y">
                @forelse($bestSellingCategories as $c)
                    <tr><td class="py-1.5">{{ $c->category }}</td><td class="py-1.5 text-right">৳{{ number_format($c->revenue, 2) }}</td></tr>
                @empty
                    <tr><td colspan="2" class="py-4 text-center text-gray-400">No data in this range.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-5">
        <h2 class="font-semibold mb-3 text-sm">Top Customers by Spend</h2>
        <table class="w-full text-xs">
            <thead class="text-gray-500 text-left"><tr><th class="pb-2">Customer</th><th class="pb-2">Orders</th><th class="pb-2 text-right">Spent</th></tr></thead>
            <tbody class="divide-y">
                @forelse($topCustomers as $c)
                    <tr><td class="py-1.5">{{ $c->name }}</td><td class="py-1.5">{{ $c->orders_count }}</td><td class="py-1.5 text-right">৳{{ number_format($c->total_spent, 2) }}</td></tr>
                @empty
                    <tr><td colspan="3" class="py-4 text-center text-gray-400">No data in this range.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    new Chart(document.getElementById('salesChart'), {
        type: 'bar',
        data: {
            labels: @json($salesSeries->pluck('bucket')),
            datasets: [{ label: 'Revenue', data: @json($salesSeries->pluck('total')), backgroundColor: '#ff5722' }]
        },
        options: { plugins: { legend: { display: false } } }
    });
</script>
@endsection
