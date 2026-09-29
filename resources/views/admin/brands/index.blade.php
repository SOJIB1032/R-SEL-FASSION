@extends('layouts.admin')
@section('title', 'Brands - ShopNova Admin')
@section('page_title', 'Brands')

@section('content')
<div class="grid md:grid-cols-3 gap-6">
    <div class="md:col-span-2 bg-white rounded-xl shadow-sm overflow-hidden h-fit">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500">
                <tr>
                    <th class="px-4 py-3">Brand</th>
                    <th class="px-4 py-3">Products</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($brands as $brand)
                    <tr>
                        <td class="px-4 py-3 font-medium flex items-center gap-2">
                            @if($brand->logo)
                                <img src="{{ asset('storage/'.$brand->logo) }}" class="w-8 h-8 rounded object-cover">
                            @endif
                            {{ $brand->name }}
                        </td>
                        <td class="px-4 py-3">{{ $brand->products_count }}</td>
                        <td class="px-4 py-3">
                            <form action="{{ route('admin.brands.toggle-active', $brand) }}" method="POST">
                                @csrf
                                <button class="text-xs font-semibold px-2 py-1 rounded-full {{ $brand->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }}">
                                    {{ $brand->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST" onsubmit="return confirm('Delete this brand?');">
                                @csrf @method('DELETE')
                                <button class="text-red-500 font-medium hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4">{{ $brands->links() }}</div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 h-fit">
        <h2 class="font-semibold mb-4">Add Brand</h2>
        <form action="{{ route('admin.brands.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
            @csrf
            <input type="text" name="name" placeholder="Brand name" required class="w-full border rounded-lg px-3 py-2 text-sm">
            <input type="file" name="logo" accept="image/*" class="w-full border rounded-lg px-3 py-2 text-sm">
            <button class="w-full bg-brand text-white rounded-lg py-2.5 font-semibold hover:bg-brand-dark">Add Brand</button>
        </form>
    </div>
</div>
@endsection
