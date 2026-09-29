<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $cards = [
            'total_revenue' => (float) Order::where('payment_status', 'paid')->sum('grand_total'),
            'total_orders' => Order::count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'total_products' => Product::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'completed_orders' => Order::where('status', 'delivered')->count(),
            'low_stock_products' => Product::whereColumn('stock_quantity', '<=', 'low_stock_threshold')->where('stock_quantity', '>', 0)->count(),
        ];

        // Sales overview — last 14 days
        $salesOverview = Order::selectRaw('DATE(created_at) as date, SUM(grand_total) as total')
            ->where('created_at', '>=', now()->subDays(13)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $salesLabels = [];
        $salesData = [];
        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $salesLabels[] = now()->subDays($i)->format('M d');
            $salesData[] = (float) ($salesOverview[$date]->total ?? 0);
        }

        // Monthly revenue — last 6 months
        $monthlyRevenue = Order::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, SUM(grand_total) as total")
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('ym')
            ->orderBy('ym')
            ->get()
            ->keyBy('ym');

        $monthLabels = [];
        $monthData = [];
        for ($i = 5; $i >= 0; $i--) {
            $ym = now()->subMonths($i)->format('Y-m');
            $monthLabels[] = now()->subMonths($i)->format('M Y');
            $monthData[] = (float) ($monthlyRevenue[$ym]->total ?? 0);
        }

        // Orders by status
        $ordersByStatus = Order::selectRaw('status, COUNT(*) as count')->groupBy('status')->pluck('count', 'status');

        // Best-selling products
        $bestSellers = Product::orderByDesc('sales_count')->limit(5)->get(['name', 'sales_count']);

        // Sales by category
        $salesByCategory = DB::table('order_items')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->selectRaw('categories.name as category, SUM(order_items.line_total) as total')
            ->groupBy('categories.name')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        return view('admin.dashboard', compact(
            'cards', 'salesLabels', 'salesData', 'monthLabels', 'monthData',
            'ordersByStatus', 'bestSellers', 'salesByCategory'
        ));
    }
}
