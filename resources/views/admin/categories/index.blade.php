@extends('layouts.admin')
@section('title', 'Categories - R-SELFASSION Admin')
@section('page_title', 'Categories')

@section('content')
<div class="flex justify-end mb-4">
    <a href="{{ route('admin.categories.create') }}" class="bg-brand text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-brand-dark">+ New Category</a>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-500">
            <tr>
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3">Products</th>
                <th class="px-4 py-3">Sort Order</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @foreach($categories as $category)
                <tr>
                    <td class="px-4 py-3 font-medium">{{ $category->name }}</td>
                    <td class="px-4 py-3">{{ $category->products_count }}</td>
                    <td class="px-4 py-3">{{ $category->sort_order }}</td>
                    <td class="px-4 py-3">
                        <form action="{{ route('admin.categories.toggle-active', $category) }}" method="POST">
                            @csrf
                            <button class="text-xs font-semibold px-2 py-1 rounded-full {{ $category->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }}">
                                {{ $category->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-4 py-3 text-right space-x-3">
                        <a href="{{ route('admin.categories.edit', $category) }}" class="text-brand font-medium hover:underline">Edit</a>
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Delete this category?');">
                            @csrf @method('DELETE')
                            <button class="text-red-500 font-medium hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $categories->links() }}</div>
@endsection
