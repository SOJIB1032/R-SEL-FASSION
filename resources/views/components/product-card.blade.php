@props(['product'])

<a href="{{ route('products.show', $product) }}" class="group bg-white rounded-xl shadow-sm hover:shadow-md transition p-3 flex flex-col">
    <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden mb-3 flex items-center justify-center">
        @if($product->primaryImage)
            <img src="{{ asset('storage/'.$product->primaryImage->path) }}" alt="{{ $product->name }}" class="object-cover w-full h-full group-hover:scale-105 transition">
        @else
            <span class="text-gray-400 text-sm">No image</span>
        @endif
    </div>

    <h3 class="text-sm font-medium text-gray-800 line-clamp-2">{{ $product->name }}</h3>

    <div class="mt-1 flex items-center gap-2">
        <span class="text-brand font-bold">৳{{ number_format($product->final_price, 2) }}</span>
        @if($product->discount_price)
            <span class="text-xs text-gray-400 line-through">৳{{ number_format($product->price, 2) }}</span>
        @endif
    </div>

    <div class="mt-1 text-xs text-gray-500">
        ⭐ {{ number_format($product->average_rating, 1) }} ({{ $product->review_count }})
        @if(! $product->is_in_stock)
            <span class="text-red-500 font-semibold ml-1">Out of stock</span>
        @endif
    </div>
</a>
