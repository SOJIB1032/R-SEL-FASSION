@extends('layouts.admin')
@section('title', 'Products - R-SELFASSION Admin')
@section('page_title', 'Products')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-4">
    <form method="GET" class="flex flex-wrap gap-2">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name or SKU..." class="border rounded-lg px-3 py-2 text-sm">
        <select name="category" class="border rounded-lg px-2 py-2 text-sm" onchange="this.form.submit()">
            <option value="">All Categories</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <select name="status" class="border rounded-lg px-2 py-2 text-sm" onchange="this.form.submit()">
            <option value="">Any Status</option>
            @foreach(['active', 'inactive', 'draft'] as $status)
                <option value="{{ $status }}" @selected(request('status') == $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        <label class="flex items-center gap-1 text-sm border rounded-lg px-3">
            <input type="checkbox" name="low_stock" value="1" @checked(request('low_stock')) onchange="this.form.submit()"> Low stock
        </label>
        <button class="bg-gray-800 text-white rounded-lg px-4 text-sm">Filter</button>
    </form>
    <a href="{{ route('admin.products.create') }}" class="bg-brand text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-brand-dark">+ New Product</a>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-500">
            <tr>
                <th class="px-4 py-3">Product</th>
                <th class="px-4 py-3">Category</th>
                <th class="px-4 py-3">Price</th>
                <th class="px-4 py-3">Stock</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @foreach($products as $product)
                <tr>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gray-100 rounded-lg overflow-hidden shrink-0">
                                @if($product->primaryImage)
                                    <img src="{{ asset('storage/'.$product->primaryImage->path) }}" class="object-cover w-full h-full">
                                @endif
                            </div>
                            <div>
                                <p class="font-medium">{{ $product->name }}</p>
                                <p class="text-xs text-gray-400">{{ $product->sku }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">{{ $product->category->name ?? '—' }}</td>
                    <td class="px-4 py-3">৳{{ number_format($product->final_price, 2) }}</td>
                    <td class="px-4 py-3">
                        <span class="{{ $product->is_low_stock ? 'text-yellow-600 font-semibold' : ($product->stock_quantity == 0 ? 'text-red-500 font-semibold' : '') }}">
                            {{ $product->stock_quantity }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <form action="{{ route('admin.products.toggle-status', $product) }}" method="POST">
                            @csrf
                            <button class="text-xs font-semibold px-2 py-1 rounded-full {{ $product->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }}">
                                {{ ucfirst($product->status) }}
                            </button>
                        </form>
                    </td>
                    <td class="px-4 py-3 text-right space-x-3">
                        <a href="{{ route('admin.products.edit', $product) }}" class="text-brand font-medium hover:underline">Edit</a>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Delete this product?');">
                            @csrf @method('DELETE')
                            <button class="text-red-500 font-medium hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $products->links() }}</div>
@endsection
