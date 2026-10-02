@extends('layouts.app')

@section('title', $product->meta_title ?? $product->name.' - R-SELFASSION')
@section('meta_description', $product->meta_description ?? Str::limit(strip_tags($product->short_description), 155))

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <nav class="text-xs text-gray-500 mb-6">
        <a href="{{ route('home') }}" class="hover:underline">Home</a> /
        <a href="{{ route('products.index', ['category' => $product->category_id]) }}" class="hover:underline">{{ $product->category->name }}</a> /
        <span class="text-gray-700">{{ $product->name }}</span>
    </nav>

    <div class="grid md:grid-cols-2 gap-10">
        <div>
            <div class="aspect-square bg-gray-100 rounded-xl overflow-hidden mb-3">
                @if($product->images->isNotEmpty())
                    <img src="{{ asset('storage/'.$product->images->first()->path) }}" alt="{{ $product->name }}" class="object-cover w-full h-full">
                @endif
            </div>
            <div class="flex gap-2">
                @foreach($product->images as $image)
                    <div class="w-16 h-16 bg-gray-100 rounded-lg overflow-hidden border">
                        <img src="{{ asset('storage/'.$image->path) }}" class="object-cover w-full h-full">
                    </div>
                @endforeach
            </div>
        </div>

        <div>
            <h1 class="text-2xl font-bold mb-2">{{ $product->name }}</h1>
            <p class="text-sm text-gray-500 mb-3">SKU: {{ $product->sku }} &bull; Brand: {{ $product->brand->name ?? 'N/A' }}</p>

            <div class="flex items-center gap-2 mb-4">
                <span class="text-yellow-500">⭐ {{ number_format($product->average_rating, 1) }}</span>
                <span class="text-sm text-gray-500">({{ $product->review_count }} reviews)</span>
            </div>

            <div class="flex items-baseline gap-3 mb-4">
                <span class="text-3xl font-extrabold text-brand">৳{{ number_format($product->final_price, 2) }}</span>
                @if($product->discount_price)
                    <span class="text-lg text-gray-400 line-through">৳{{ number_format($product->price, 2) }}</span>
                @endif
            </div>

            <p class="text-sm mb-2">
                @if($product->is_in_stock)
                    <span class="text-green-600 font-semibold">In Stock</span> ({{ $product->stock_quantity }} available)
                @else
                    <span class="text-red-500 font-semibold">Out of Stock</span>
                @endif
            </p>

            <p class="text-gray-600 mb-6">{{ $product->short_description }}</p>

            @if($product->variants->isNotEmpty())
                <div class="mb-6">
                    <h3 class="text-sm font-semibold mb-2">Select Variant</h3>
                    <div class="flex flex-wrap gap-2" id="variant-options">
                        @foreach($product->variants as $variant)
                            <button type="button" class="variant-btn border rounded-lg px-3 py-1.5 text-sm hover:border-brand" data-variant-id="{{ $variant->id }}">
                                {{ $variant->label }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 text-red-700 text-xs rounded-lg p-3 mb-3">
                    @foreach ($errors->all() as $error) <p>{{ $error }}</p> @endforeach
                </div>
            @endif

            <form action="{{ route('cart.store') }}" method="POST" class="flex items-center gap-3 mb-4">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="product_variant_id" id="selected-variant-id" value="">
                <input type="number" name="quantity" min="1" max="{{ $product->stock_quantity }}" value="1" class="w-20 border rounded-lg px-3 py-2 text-sm">
                <button type="submit" class="flex-1 bg-brand text-white rounded-lg py-2.5 font-semibold hover:bg-brand-dark" @disabled(!$product->is_in_stock)>Add to Cart</button>
                @auth
                    <button type="submit" formaction="{{ route('wishlist.store', $product) }}" class="border rounded-lg px-3 py-2.5" title="Add to wishlist">♥</button>
                @else
                    <a href="{{ route('login') }}" class="border rounded-lg px-3 py-2.5" title="Log in to save to wishlist">♥</a>
                @endauth
            </form>

            <script>
                document.querySelectorAll('.variant-btn').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        document.querySelectorAll('.variant-btn').forEach(b => b.classList.remove('border-brand', 'bg-orange-50'));
                        this.classList.add('border-brand', 'bg-orange-50');
                        document.getElementById('selected-variant-id').value = this.dataset.variantId;
                    });
                });
            </script>
        </div>
    </div>

    <div class="mt-12">
        <h2 class="text-xl font-bold mb-4">Description</h2>
        <div class="prose max-w-none text-gray-700">{!! $product->description !!}</div>
    </div>

    <div class="mt-12">
        <h2 class="text-xl font-bold mb-4">Customer Reviews ({{ $product->review_count }})</h2>
        @forelse($product->approvedReviews as $review)
            <div class="border-b py-4">
                <div class="flex items-center gap-2 mb-1">
                    <span class="font-semibold text-sm">{{ $review->user->name }}</span>
                    <span class="text-yellow-500 text-sm">{{ str_repeat('⭐', $review->rating) }}</span>
                </div>
                <p class="font-medium text-sm">{{ $review->title }}</p>
                <p class="text-sm text-gray-600">{{ $review->description }}</p>
            </div>
        @empty
            <p class="text-sm text-gray-500">No reviews yet. Be the first to review this product after purchase.</p>
        @endforelse
    </div>

    @if($relatedProducts->isNotEmpty())
        <div class="mt-12">
            <h2 class="text-xl font-bold mb-4">Related Products</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($relatedProducts as $related)
                    <x-product-card :product="$related" />
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
