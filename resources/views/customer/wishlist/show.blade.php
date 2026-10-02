@extends('layouts.app')
@section('title', 'Your Wishlist - R-SELFASSION')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Your Wishlist</h1>

    @if (session('status'))
        <div class="bg-green-50 text-green-700 text-sm rounded-lg p-3 mb-4">{{ session('status') }}</div>
    @endif

    @if($wishlist->items->isEmpty())
        <div class="bg-white rounded-xl shadow-sm p-12 text-center text-gray-500">
            <p class="text-lg font-medium mb-2">Your wishlist is empty</p>
            <a href="{{ route('products.index') }}" class="text-brand font-medium hover:underline">Browse products →</a>
        </div>
    @else
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($wishlist->items as $item)
                <div class="bg-white rounded-xl shadow-sm p-3 flex flex-col">
                    <a href="{{ route('products.show', $item->product) }}" class="block aspect-square bg-gray-100 rounded-lg overflow-hidden mb-3">
                        @if($item->product->primaryImage)
                            <img src="{{ asset('storage/'.$item->product->primaryImage->path) }}" class="object-cover w-full h-full">
                        @endif
                    </a>
                    <a href="{{ route('products.show', $item->product) }}" class="text-sm font-medium line-clamp-2 hover:text-brand">{{ $item->product->name }}</a>
                    <p class="text-brand font-bold mt-1">৳{{ number_format($item->product->final_price, 2) }}</p>

                    <div class="flex gap-2 mt-3">
                        <form action="{{ route('wishlist.move-to-cart', $item->product) }}" method="POST" class="flex-1">
                            @csrf
                            <button class="w-full bg-brand text-white text-xs rounded-lg py-2 font-semibold hover:bg-brand-dark" @disabled(!$item->product->is_in_stock)>
                                Move to Cart
                            </button>
                        </form>
                        <form action="{{ route('wishlist.destroy', $item->product) }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="border rounded-lg px-2 py-2 text-xs text-gray-500 hover:text-red-500">✕</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
