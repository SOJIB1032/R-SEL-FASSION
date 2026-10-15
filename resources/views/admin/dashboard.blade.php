@extends('layouts.admin')
@section('title', 'Dashboard - R-SELFASSION Admin')
@section('page_title', 'Dashboard')

@section('content')
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    @foreach([
        ['label' => 'Total Revenue', 'value' => '৳'.number_format($cards['total_revenue'], 2), 'color' => 'text-brand'],
        ['label' => 'Total Orders', 'value' => $cards['total_orders'], 'color' => 'text-gray-800'],
        ['label' => 'Total Customers', 'value' => $cards['total_customers'], 'color' => 'text-gray-800'],
        ['label' => 'Total Products', 'value' => $cards['total_products'], 'color' => 'text-gray-800'],
        ['label' => 'Pending Orders', 'value' => $cards['pending_orders'], 'color' => 'text-yellow-600'],
        ['label' => 'Completed Orders', 'value' => $cards['completed_orders'], 'color' => 'text-green-600'],
        ['label' => 'Low Stock Products', 'value' => $cards['low_stock_products'], 'color' => 'text-red-600'],
    ] as $card)
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-xs text-gray-500 mb-1">{{ $card['label'] }}</p>
            <p class="text-2xl font-bold {{ $card['color'] }}">{{ $card['value'] }}</p>
        </div>
    @endforeach
</div>

<div class="grid md:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-5">
        <h2 class="font-semibold mb-3 text-sm">Sales Overview (14 days)</h2>
        <canvas id="salesOverviewChart" height="180"></canvas>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5">
        <h2 class="font-semibold mb-3 text-sm">Monthly Revenue (6 months)</h2>
        <canvas id="monthlyRevenueChart" height="180"></canvas>
    </div>
</div>

<div class="grid md:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl shadow-sm p-5">
        <h2 class="font-semibold mb-3 text-sm">Orders by Status</h2>
        <canvas id="ordersByStatusChart" height="200"></canvas>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5">
        <h2 class="font-semibold mb-3 text-sm">Best-Selling Products</h2>
        <ul class="text-sm space-y-2">
            @forelse($bestSellers as $p)
                <li class="flex justify-between border-b pb-2 last:border-0"><span>{{ $p->name }}</span><span class="font-semibold">{{ $p->sales_count }} sold</span></li>
            @empty
                <li class="text-gray-500">No sales yet.</li>
            @endforelse
        </ul>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5">
        <h2 class="font-semibold mb-3 text-sm">Sales by Category</h2>
        <canvas id="salesByCategoryChart" height="200"></canvas>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof window.Chart === 'undefined') {
        console.error('Chart.js is not loaded. Check resources/js/app.js and Vite build.');
        return;
    }

    const brand = '#ff5722';

    new window.Chart(document.getElementById('salesOverviewChart'), {
        type: 'line',
        data: {
            labels: @json($salesLabels),
            datasets: [{
                label: 'Revenue',
                data: @json($salesData),
                borderColor: brand,
                backgroundColor: 'rgba(255,87,34,0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            plugins: {
                legend: { display: false }
            }
        }
    });

    new window.Chart(document.getElementById('monthlyRevenueChart'), {
        type: 'bar',
        data: {
            labels: @json($monthLabels),
            datasets: [{
                label: 'Revenue',
                data: @json($monthData),
                backgroundColor: brand
            }]
        },
        options: {
            plugins: {
                legend: { display: false }
            }
        }
    });

    new window.Chart(document.getElementById('ordersByStatusChart'), {
        type: 'doughnut',
        data: {
            labels: @json($ordersByStatus->keys()),
            datasets: [{
                data: @json($ordersByStatus->values()),
                backgroundColor: [
                    '#facc15', '#60a5fa', '#818cf8',
                    '#34d399', '#4ade80', '#f87171', '#9ca3af'
                ]
            }]
        }
    });

    new window.Chart(document.getElementById('salesByCategoryChart'), {
        type: 'pie',
        data: {
            labels: @json($salesByCategory->pluck('category')),
            datasets: [{
                data: @json($salesByCategory->pluck('total')),
                backgroundColor: [
                    '#ff5722', '#facc15', '#60a5fa',
                    '#818cf8', '#34d399', '#f87171'
                ]
            }]
        }
    });
});
</script>
@endsection
