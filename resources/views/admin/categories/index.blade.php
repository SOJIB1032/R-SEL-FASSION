@extends('layouts.admin')
@section('title', 'Categories - ShopNova Admin')
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

<section class="mt-8 grid xl:grid-cols-3 gap-6">
    <div class="xl:col-span-2 bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-5 border-b"><h2 class="font-semibold text-lg">Sub-categories</h2></div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left text-gray-500"><tr><th class="px-4 py-3">Name</th><th class="px-4 py-3">Category</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Actions</th></tr></thead>
                <tbody class="divide-y">
                @forelse($subCategories as $subCategory)
                    <tr>
                        <td class="px-4 py-3">{{ $subCategory->name }}</td>
                        <td class="px-4 py-3">{{ $subCategory->category?->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <form action="{{ route('admin.sub-categories.toggle-active', $subCategory) }}" method="POST">@csrf
                                <button class="text-xs rounded-full px-2 py-1 {{ $subCategory->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }}">{{ $subCategory->is_active ? 'Active' : 'Inactive' }}</button>
                            </form>
                        </td>
                        <td class="px-4 py-3">
                            <details>
                                <summary class="cursor-pointer text-brand">Edit</summary>
                                <form action="{{ route('admin.sub-categories.update', $subCategory) }}" method="POST" enctype="multipart/form-data" class="space-y-2 mt-2 min-w-52">
                                    @csrf @method('PUT')
                                    <select name="category_id" required class="w-full border rounded p-1">
                                        @foreach($allCategories as $option)<option value="{{ $option->id }}" @selected($option->id === $subCategory->category_id)>{{ $option->name }}</option>@endforeach
                                    </select>
                                    <input name="name" value="{{ $subCategory->name }}" required maxlength="255" class="w-full border rounded p-1">
                                    <textarea name="description" class="w-full border rounded p-1" placeholder="Description">{{ $subCategory->description }}</textarea>
                                    <input type="file" name="image" accept="image/*" class="w-full text-xs">
                                    <label class="flex gap-2 items-center text-xs"><input type="checkbox" name="is_active" value="1" @checked($subCategory->is_active)> Active</label>
                                    <button class="bg-brand text-white rounded px-3 py-1">Save</button>
                                </form>
                            </details>
                            <form action="{{ route('admin.sub-categories.destroy', $subCategory) }}" method="POST" class="inline" onsubmit="return confirm('Delete this sub-category?')">@csrf @method('DELETE')<button class="text-red-500 ml-2">Delete</button></form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">No sub-categories yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <form action="{{ route('admin.sub-categories.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm p-5 space-y-3 h-fit">
        @csrf
        <h2 class="font-semibold text-lg">Add sub-category</h2>
        <label class="block text-sm">Parent category<select name="category_id" required class="mt-1 w-full border rounded-lg p-2">
            <option value="">Select category</option>
            @foreach($allCategories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach
        </select></label>
        <label class="block text-sm">Name<input name="name" required maxlength="255" class="mt-1 w-full border rounded-lg p-2"></label>
        <label class="block text-sm">Description<textarea name="description" class="mt-1 w-full border rounded-lg p-2"></textarea></label>
        <label class="block text-sm">Image<input type="file" name="image" accept="image/*" class="mt-1 w-full border rounded-lg p-2"></label>
        <label class="flex gap-2 items-center text-sm"><input type="checkbox" name="is_active" value="1" checked> Active</label>
        <button class="w-full bg-brand text-white rounded-lg py-2 font-semibold">Add sub-category</button>
    </form>
</section>

@endsection
