@extends('layouts.admin')
@section('title', ($product->exists ? 'Edit' : 'New').' Product - R-SELFASSION Admin')
@section('page_title', ($product->exists ? 'Edit' : 'New').' Product')

@section('content')
<form action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="grid md:grid-cols-3 gap-6">
    @csrf
    @if($product->exists) @method('PUT') @endif

    <div class="md:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
            <h2 class="font-semibold">Basic Information</h2>
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium mb-1">Product Name</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">SKU</label>
                    <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" required class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Status</label>
                    <select name="status" class="w-full border rounded-lg px-3 py-2">
                        @foreach(['active', 'inactive', 'draft'] as $status)
                            <option value="{{ $status }}" @selected(old('status', $product->status ?? 'active') == $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Category</label>
                    <select name="category_id" required class="w-full border rounded-lg px-3 py-2">
                        <option value="">Select...</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Sub-Category</label>
                    <select name="sub_category_id" class="w-full border rounded-lg px-3 py-2">
                        <option value="">None</option>
                        @foreach($subCategories as $sub)
                            <option value="{{ $sub->id }}" @selected(old('sub_category_id', $product->sub_category_id) == $sub->id)>{{ $sub->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Brand</label>
                    <select name="brand_id" class="w-full border rounded-lg px-3 py-2">
                        <option value="">None</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" @selected(old('brand_id', $product->brand_id) == $brand->id)>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium mb-1">Short Description</label>
                    <input type="text" name="short_description" value="{{ old('short_description', $product->short_description) }}" class="w-full border rounded-lg px-3 py-2">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium mb-1">Full Description</label>
                    <textarea name="description" rows="6" class="w-full border rounded-lg px-3 py-2">{{ old('description', $product->description) }}</textarea>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
            <h2 class="font-semibold">Pricing &amp; Stock</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Price (৳)</label>
                    <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}" required class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Discount Price (৳)</label>
                    <input type="number" step="0.01" name="discount_price" value="{{ old('discount_price', $product->discount_price) }}" class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Stock Quantity</label>
                    <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}" required class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Low Stock Threshold</label>
                    <input type="number" name="low_stock_threshold" value="{{ old('low_stock_threshold', $product->low_stock_threshold ?? 5) }}" class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Weight (kg)</label>
                    <input type="number" step="0.01" name="weight" value="{{ old('weight', $product->weight) }}" class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Dimensions</label>
                    <input type="text" name="dimensions" value="{{ old('dimensions', $product->dimensions) }}" placeholder="L x W x H cm" class="w-full border rounded-lg px-3 py-2">
                </div>
            </div>
        </div>

       <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
    <h2 class="font-semibold">Images</h2>

    @if($product->exists && $product->images->isNotEmpty())
        <div class="flex flex-wrap gap-3 mb-3">

            @foreach($product->images as $image)
                <div class="relative w-24 h-24">

                    <img
                        src="{{ asset('storage/' . $image->path) }}"
                        alt="{{ $product->name }}"
                        class="w-full h-full object-cover rounded-lg border
                        {{ $image->is_primary ? 'border-brand border-2' : '' }}"
                    >

                    @if($image->is_primary)
                        <span class="absolute bottom-1 left-1 bg-brand text-white text-[10px] px-1.5 py-0.5 rounded">
                            Primary
                        </span>
                    @endif

                    <button
                        type="button"
                        onclick="deleteProductImage({{ $image->id }})"
                        class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 text-xs flex items-center justify-center"
                        title="Delete image"
                    >
                        ✕
                    </button>

                </div>
            @endforeach

        </div>
    @endif

    <input
        type="file"
        name="images[]"
        accept="image/*"
        multiple
        class="w-full border rounded-lg px-3 py-2 text-sm"
    >

    <p class="text-xs text-gray-400">
        You can select multiple images. The first uploaded image becomes the primary image if no primary image exists.
    </p>
</div>

        <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold">Variants (Size / Color)</h2>
                <button type="button" id="add-variant" class="text-brand text-sm font-medium hover:underline">+ Add Variant Row</button>
            </div>
            <div id="variant-rows" class="space-y-2">
                @foreach(old('variants', $product->exists ? $product->variants->toArray() : []) as $i => $variant)
                    <div class="grid grid-cols-6 gap-2 items-center variant-row">
                        <input type="hidden" name="variants[{{ $i }}][id]" value="{{ $variant['id'] ?? '' }}">
                        <input type="text" name="variants[{{ $i }}][size]" value="{{ $variant['size'] ?? '' }}" placeholder="Size" class="border rounded-lg px-2 py-1.5 text-sm">
                        <input type="text" name="variants[{{ $i }}][color]" value="{{ $variant['color'] ?? '' }}" placeholder="Color" class="border rounded-lg px-2 py-1.5 text-sm">
                        <input type="text" name="variants[{{ $i }}][sku]" value="{{ $variant['sku'] ?? '' }}" placeholder="SKU (auto if blank)" class="border rounded-lg px-2 py-1.5 text-sm">
                        <input type="number" step="0.01" name="variants[{{ $i }}][price_adjustment]" value="{{ $variant['price_adjustment'] ?? 0 }}" placeholder="+/- Price" class="border rounded-lg px-2 py-1.5 text-sm">
                        <input type="number" name="variants[{{ $i }}][stock_quantity]" value="{{ $variant['stock_quantity'] ?? 0 }}" placeholder="Stock" class="border rounded-lg px-2 py-1.5 text-sm">
                        <button type="button" class="text-red-500 text-xs remove-variant">Remove</button>
                    </div>
                @endforeach
            </div>
            <p class="text-xs text-gray-400">Leave both Size and Color blank on a row to skip it. Clear all rows and save to remove all variants.</p>
        </div>
    </div>

    <div class="space-y-6 h-fit">
        <div class="bg-white rounded-xl shadow-sm p-6 space-y-3">
            <h2 class="font-semibold mb-2">Visibility</h2>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured ?? false))> Featured
            </label>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_new_arrival" value="1" @checked(old('is_new_arrival', $product->is_new_arrival ?? false))> New Arrival
            </label>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_best_seller" value="1" @checked(old('is_best_seller', $product->is_best_seller ?? false))> Best Seller
            </label>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 space-y-3">
            <h2 class="font-semibold mb-2">SEO</h2>
            <div>
                <label class="block text-xs font-medium mb-1">Meta Title</label>
                <input type="text" name="meta_title" value="{{ old('meta_title', $product->meta_title) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium mb-1">Meta Description</label>
                <textarea name="meta_description" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm">{{ old('meta_description', $product->meta_description) }}</textarea>
            </div>
        </div>

        <button class="w-full bg-brand text-white rounded-lg py-2.5 font-semibold hover:bg-brand-dark">
            {{ $product->exists ? 'Update' : 'Create' }} Product
        </button>
    </div>
</form>

<script>
    let variantIndex = {{ count(old('variants', $product->exists ? $product->variants->toArray() : [])) }};
    document.getElementById('add-variant').addEventListener('click', function () {
        const row = document.createElement('div');
        row.className = 'grid grid-cols-6 gap-2 items-center variant-row';
        row.innerHTML = `
            <input type="hidden" name="variants[${variantIndex}][id]" value="">
            <input type="text" name="variants[${variantIndex}][size]" placeholder="Size" class="border rounded-lg px-2 py-1.5 text-sm">
            <input type="text" name="variants[${variantIndex}][color]" placeholder="Color" class="border rounded-lg px-2 py-1.5 text-sm">
            <input type="text" name="variants[${variantIndex}][sku]" placeholder="SKU (auto if blank)" class="border rounded-lg px-2 py-1.5 text-sm">
            <input type="number" step="0.01" name="variants[${variantIndex}][price_adjustment]" value="0" placeholder="+/- Price" class="border rounded-lg px-2 py-1.5 text-sm">
            <input type="number" name="variants[${variantIndex}][stock_quantity]" value="0" placeholder="Stock" class="border rounded-lg px-2 py-1.5 text-sm">
            <button type="button" class="text-red-500 text-xs remove-variant">Remove</button>
        `;
        document.getElementById('variant-rows').appendChild(row);
        variantIndex++;
    });
    document.getElementById('variant-rows').addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-variant')) {
            e.target.closest('.variant-row').remove();
        }
    });
</script>
@endsection
<script>
    function deleteProductImage(imageId) {

        if (!confirm('Are you sure you want to delete this image?')) {
            return;
        }

        fetch(`/admin/products/images/${imageId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to delete image');
            }

            return response.json().catch(() => ({}));
        })
        .then(() => {
            window.location.reload();
        })
        .catch(error => {
            console.error(error);
            alert('Could not delete the image.');
        });
    }
</script>
