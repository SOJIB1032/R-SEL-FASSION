@extends('layouts.app')

@section('title', 'R-SELFASSION - Shop Smarter, Live Better')

@section('content')
    <section class="bg-gradient-to-r from-brand to-orange-400 text-white">
        <div class="max-w-7xl mx-auto px-4 py-16 grid md:grid-cols-2 items-center gap-8">
            <div>
                <h1 class="text-4xl font-extrabold mb-4">Everything you need, delivered fast.</h1>
                <p class="mb-6 text-orange-50">Discover thousands of products across every category, at prices that make sense.</p>
                <a href="{{ route('products.index') }}" class="bg-white text-brand font-semibold px-6 py-3 rounded-lg hover:bg-orange-50">Shop Now</a>
            </div>
            <div class="hidden md:block bg-white/10 rounded-xl h-56"></div>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 py-10 space-y-14">

        @if($categories->count())
        <section>
            <h2 class="text-xl font-bold mb-4">Shop by Category</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4">
                @foreach($categories as $category)
                    <a href="{{ route('products.index', ['category' => $category->id]) }}" class="bg-white rounded-xl p-4 text-center shadow-sm hover:shadow-md transition">
                        <div class="w-full aspect-square bg-gray-100 rounded-lg mb-2"></div>
                        <span class="text-sm font-medium">{{ $category->name }}</span>
                    </a>
                @endforeach
            </div>
        </section>
        @endif

        @foreach([['Featured Products', $featured], ['New Arrivals', $newArrivals], ['Best Sellers', $bestSellers], ['On Sale', $onSale]] as [$title, $items])
            @if($items->count())
                <section>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold">{{ $title }}</h2>
                        <a href="{{ route('products.index') }}" class="text-brand text-sm font-medium hover:underline">View all</a>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($items as $product)
                            <x-product-card :product="$product" />
                        @endforeach
                    </div>
                </section>
            @endif
        @endforeach

    </div>
@endsection
