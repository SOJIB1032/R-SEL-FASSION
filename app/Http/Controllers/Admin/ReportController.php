<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $period = $request->string('period')->value() ?: 'daily';
        $from = $request->filled('from') ? $request->date('from') : now()->subDays(29)->startOfDay();
        $to = $request->filled('to') ? $request->date('to')->endOfDay() : now()->endOfDay();

        $paidOrders = Order::whereBetween('created_at', [$from, $to]);

        $format = match ($period) {
            'weekly' => '%x-W%v',
            'monthly' => '%Y-%m',
            'yearly' => '%Y',
            default => '%Y-%m-%d',
        };

        $salesSeries = (clone $paidOrders)
            ->selectRaw("DATE_FORMAT(created_at, '{$format}') as bucket, SUM(grand_total) as total, COUNT(*) as orders")
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->get();

        $summary = [
            'total_revenue' => (clone $paidOrders)->where('payment_status', 'paid')->sum('grand_total'),
            'total_orders' => (clone $paidOrders)->count(),
            'average_order_value' => (clone $paidOrders)->avg('grand_total') ?? 0,
        ];

        $bestSellingProducts = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereBetween('orders.created_at', [$from, $to])
            ->selectRaw('order_items.product_name, SUM(order_items.quantity) as units, SUM(order_items.line_total) as revenue')
            ->groupBy('order_items.product_name')
            ->orderByDesc('units')
            ->limit(10)
            ->get();

        $bestSellingCategories = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->whereBetween('orders.created_at', [$from, $to])
            ->selectRaw('categories.name as category, SUM(order_items.line_total) as revenue')
            ->groupBy('categories.name')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        $topCustomers = DB::table('orders')
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->whereBetween('orders.created_at', [$from, $to])
            ->selectRaw('users.name, users.email, COUNT(orders.id) as orders_count, SUM(orders.grand_total) as total_spent')
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();

        return view('admin.reports.index', compact(
            'period', 'from', 'to', 'salesSeries', 'summary',
            'bestSellingProducts', 'bestSellingCategories', 'topCustomers'
        ));
    }
}
