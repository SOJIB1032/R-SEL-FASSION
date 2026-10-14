@extends('layouts.admin')
@section('title', 'Coupons - ShopNova Admin')
@section('page_title', 'Coupons')

@section('content')
<div class="grid md:grid-cols-3 gap-6">
    <div class="md:col-span-2 bg-white rounded-xl shadow-sm overflow-hidden h-fit">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500">
                <tr>
                    <th class="px-4 py-3">Code</th>
                    <th class="px-4 py-3">Discount</th>
                    <th class="px-4 py-3">Min Order</th>
                    <th class="px-4 py-3">Used</th>
                    <th class="px-4 py-3">Expires</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($coupons as $coupon)
                    <tr>
                        <td class="px-4 py-3 font-mono font-semibold">{{ $coupon->code }}</td>
                        <td class="px-4 py-3">{{ $coupon->type === 'percentage' ? $coupon->value.'%' : '৳'.number_format($coupon->value, 2) }}</td>
                        <td class="px-4 py-3">৳{{ number_format($coupon->min_order_amount, 2) }}</td>
                        <td class="px-4 py-3">{{ $coupon->times_used }}{{ $coupon->usage_limit ? ' / '.$coupon->usage_limit : '' }}</td>
                        <td class="px-4 py-3">{{ $coupon->expires_at?->format('M d, Y') ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <form action="{{ route('admin.coupons.toggle-active', $coupon) }}" method="POST">
                                @csrf
                                <button class="text-xs font-semibold px-2 py-1 rounded-full {{ $coupon->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }}">
                                    {{ $coupon->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <details class="inline-block text-left">
                                <summary class="cursor-pointer text-brand font-medium">Edit</summary>
                                <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST" class="mt-2 bg-white border rounded-lg p-3 w-64 space-y-2">
                                    @csrf @method('PUT')
                                    <input name="code" value="{{ $coupon->code }}" required maxlength="50" class="w-full border rounded px-2 py-1 text-sm uppercase">
                                    <select name="type" class="w-full border rounded px-2 py-1 text-sm">
                                        <option value="percentage" @selected($coupon->type === 'percentage')>Percentage %</option>
                                        <option value="fixed" @selected($coupon->type === 'fixed')>Fixed amount ৳</option>
                                    </select>
                                    <input type="number" step="0.01" min="0" name="value" value="{{ $coupon->value }}" required class="w-full border rounded px-2 py-1 text-sm">
                                    <input type="number" step="0.01" min="0" name="min_order_amount" value="{{ $coupon->min_order_amount }}" placeholder="Minimum order" class="w-full border rounded px-2 py-1 text-sm">
                                    <input type="number" step="0.01" min="0" name="max_discount_amount" value="{{ $coupon->max_discount_amount }}" placeholder="Maximum discount" class="w-full border rounded px-2 py-1 text-sm">
                                    <label class="block text-xs">Starts at<input type="datetime-local" name="starts_at" value="{{ $coupon->starts_at?->format('Y-m-d\TH:i') }}" class="w-full border rounded px-2 py-1"></label>
                                    <label class="block text-xs">Expires at<input type="datetime-local" name="expires_at" value="{{ $coupon->expires_at?->format('Y-m-d\TH:i') }}" class="w-full border rounded px-2 py-1"></label>
                                    <input type="number" min="1" name="usage_limit" value="{{ $coupon->usage_limit }}" placeholder="Usage limit" class="w-full border rounded px-2 py-1 text-sm">
                                    <input type="number" min="1" name="per_user_limit" value="{{ $coupon->per_user_limit }}" placeholder="Per-user limit" class="w-full border rounded px-2 py-1 text-sm">
                                    <label class="flex items-center gap-2 text-xs"><input type="checkbox" name="is_active" value="1" @checked($coupon->is_active)> Active</label>
                                    <button class="bg-brand text-white rounded px-3 py-1 text-sm">Save changes</button>
                                </form>
                            </details>
                            <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="inline" onsubmit="return confirm('Delete this coupon?');">
                                @csrf @method('DELETE')
                                <button class="text-red-500 font-medium hover:underline ml-2">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4">{{ $coupons->links() }}</div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 h-fit">
        <h2 class="font-semibold mb-4">New Coupon</h2>
        <form action="{{ route('admin.coupons.store') }}" method="POST" class="space-y-3">
            @csrf
            <input type="text" name="code" placeholder="Coupon Code (e.g. SAVE20)" required class="w-full border rounded-lg px-3 py-2 text-sm uppercase">
            <select name="type" class="w-full border rounded-lg px-3 py-2 text-sm">
                <option value="percentage">Percentage %</option>
                <option value="fixed">Fixed Amount ৳</option>
            </select>
            <input type="number" step="0.01" name="value" placeholder="Value" required class="w-full border rounded-lg px-3 py-2 text-sm">
            <input type="number" step="0.01" name="min_order_amount" placeholder="Minimum Order Amount" class="w-full border rounded-lg px-3 py-2 text-sm">
            <input type="number" step="0.01" name="max_discount_amount" placeholder="Max Discount Cap (for %)" class="w-full border rounded-lg px-3 py-2 text-sm">
            <div class="grid grid-cols-2 gap-2">
                <input type="date" name="starts_at" class="w-full border rounded-lg px-3 py-2 text-sm">
                <input type="date" name="expires_at" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="grid grid-cols-2 gap-2">
                <input type="number" name="usage_limit" placeholder="Total Usage Limit" class="w-full border rounded-lg px-3 py-2 text-sm">
                <input type="number" name="per_user_limit" placeholder="Per-User Limit" value="1" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <button class="w-full bg-brand text-white rounded-lg py-2.5 font-semibold hover:bg-brand-dark">Create Coupon</button>
        </form>
    </div>
</div>
@endsection
