@extends('layouts.app')

@section('title', 'R-SELFASSION - Shop Smarter, Live Better')

@section('content')
    <section style="background-color: #B8CCDF; color: #212529;" class=" text-white">
        <div class="max-w-7xl mx-auto px-4 py-16 grid md:grid-cols-2 items-center gap-8">
            <div>
                <h1 class="text-4xl font-extrabold mb-4"> Redefine Your Style. <br>  <span class="text-blue-900">Wear Your Confidence.</span></h1>
                <p class="mb-6 text-black">Discover thousands of products across every category, at prices that make sense.</p>
                <div class="mb-6">
<!-- add icon section here -->
<div class="flex items-center justify-start gap-0 mt-4 text-center">

    <!-- Fast Delivery -->
    <div class="flex flex-col items-center justify-center px-3 py-1 border-r border-gray-300">
        <i class="fa-solid fa-truck-fast text-blue-900 text-lg mb-1"></i>
        <span class="text-xs text-gray-800 leading-tight">
            Fast<br>Delivery
        </span>
    </div>

    <!-- Secure Payment -->
    <div class="flex flex-col items-center justify-center px-3 py-1 border-r border-gray-300">
        <i class="fa-solid fa-shield-halved text-blue-900 text-lg mb-1"></i>
        <span class="text-xs text-gray-800 leading-tight">
            Secure<br>Payment
        </span>
    </div>

    <!-- Easy Returns -->
    <div class="flex flex-col items-center justify-center px-3 py-1 border-r border-gray-300">
        <i class="fa-solid fa-rotate text-blue-900 text-lg mb-1"></i>
        <span class="text-xs text-gray-800 leading-tight">
            Easy<br>Returns
        </span>
    </div>

    <!-- 24/7 Support -->
    <div class="flex flex-col items-center justify-center px-3 py-1">
        <i class="fa-solid fa-headset text-blue-900 text-lg mb-1"></i>
        <span class="text-xs text-gray-800 leading-tight">
            24/7<br>Support
        </span>
    </div>

</div>
<!-- close icon section -->
                </div>
             
<a href="{{ route('products.index') }}"
   class="inline-flex items-center gap-3 bg-blue-600 text-white font-semibold px-8 py-3 rounded-full hover:bg-blue-800 hover:shadow-lg transition-all duration-300">
    Shop Now
    <i class="fa-solid fa-arrow-right"></i>
</a>

            </div>
            <div class="hidden md:block bg-white/10 rounded-xl h-56">
                <img src="{{ asset('images/R-SELFASSION Fashion Banner.png') }}" alt="ShopNova Banner" class="object-cover w-full h-full rounded-xl">
                
            </div>
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
        
@if($flashSaleProducts->isNotEmpty())
    <section class="border border-orange-200 bg-orange-50 rounded-2xl p-5 md:p-6">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-orange-600">
                    Limited Time Offer
                </span>
                <h2 class="text-2xl font-extrabold text-gray-900">
                    ⚡ Flash Sale
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Grab these deals before they expire.
                </p>
            </div>

            <span class="text-sm font-semibold text-orange-700">
                Ends soon
            </span>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($flashSaleProducts as $product)
                <div class="relative">
                    <span class="absolute top-2 left-2 z-10 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded">
                        FLASH SALE
                    </span>

                    <x-product-card :product="$product" />
                </div>
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
