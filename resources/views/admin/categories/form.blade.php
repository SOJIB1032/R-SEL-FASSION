@extends('layouts.admin')
@section('title', ($category->exists ? 'Edit' : 'New').' Category - R-SELFASSION Admin')
@section('page_title', ($category->exists ? 'Edit' : 'New').' Category')

@section('content')
<div class="max-w-2xl">
    <form action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm p-6 space-y-4">
        @csrf
        @if($category->exists) @method('PUT') @endif

        <div>
            <label class="block text-sm font-medium mb-1">Name</label>
            <input type="text" name="name" value="{{ old('name', $category->name) }}" required class="w-full border rounded-lg px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Description</label>
            <textarea name="description" rows="3" class="w-full border rounded-lg px-3 py-2">{{ old('description', $category->description) }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Image</label>
            @if($category->image)
                <img src="{{ asset('storage/'.$category->image) }}" class="w-20 h-20 object-cover rounded-lg mb-2">
            @endif
            <input type="file" name="image" accept="image/*" class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Sort Order</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}" class="w-32 border rounded-lg px-3 py-2">
        </div>

        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active ?? true))> Active
        </label>

        <button class="bg-brand text-white rounded-lg px-6 py-2.5 font-semibold hover:bg-brand-dark">
            {{ $category->exists ? 'Update' : 'Create' }} Category
        </button>
    </form>

    @if($category->exists)
        <div class="bg-white rounded-xl shadow-sm p-6 mt-6">
            <h2 class="font-semibold mb-4">Sub-Categories</h2>
            <div class="space-y-2 mb-4">
                @forelse($category->subCategories as $sub)
                    <div class="flex items-center justify-between border rounded-lg px-3 py-2 text-sm">
                        <span>{{ $sub->name }}</span>
                        <form action="{{ route('admin.sub-categories.destroy', $sub) }}" method="POST" onsubmit="return confirm('Delete this sub-category?');">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:underline">Remove</button>
                        </form>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No sub-categories yet.</p>
                @endforelse
            </div>
            <form action="{{ route('admin.sub-categories.store') }}" method="POST" class="flex gap-2">
                @csrf
                <input type="hidden" name="category_id" value="{{ $category->id }}">
                <input type="text" name="name" placeholder="New sub-category name" required class="flex-1 border rounded-lg px-3 py-2 text-sm">
                <button class="bg-gray-800 text-white rounded-lg px-4 text-sm font-semibold">Add</button>
            </form>
        </div>
    @endif
</div>
@endsection
