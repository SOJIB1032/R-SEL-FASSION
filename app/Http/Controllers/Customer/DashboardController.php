<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $stats = [
            'total_orders' => $user->orders()->count(),
            'pending_orders' => $user->orders()->whereIn('status', ['pending', 'confirmed', 'processing', 'shipped'])->count(),
            'completed_orders' => $user->orders()->where('status', 'delivered')->count(),
            'wishlist_items' => optional($user->wishlist)->items()->count() ?? 0,
        ];

        $recentOrders = $user->orders()->latest()->limit(5)->get();

        return view('customer.dashboard', compact('stats', 'recentOrders'));
    }
}
