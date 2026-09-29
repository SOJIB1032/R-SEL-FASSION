<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::where('role', 'customer')->withCount('orders');

        if ($search = $request->string('q')->trim()->value()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->latest()->paginate(15)->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $customer): View
    {
        abort_unless($customer->role === 'customer', 404);
        $customer->load(['orders' => fn ($q) => $q->latest()->limit(20), 'addresses']);

        return view('admin.customers.show', compact('customer'));
    }

    public function toggleActive(User $customer): RedirectResponse
    {
        abort_unless($customer->role === 'customer', 404);
        $customer->update(['is_active' => ! $customer->is_active]);

        return back()->with('status', 'Customer account status updated.');
    }
}
