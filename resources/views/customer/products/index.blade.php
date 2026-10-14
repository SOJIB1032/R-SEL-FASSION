@extends('layouts.app')

@section('title', 'Shop All Products - ShopNova')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 grid md:grid-cols-4 gap-8">

    <aside class="md:col-span-1">
        <form method="GET" action="{{ route('products.index') }}" class="bg-white rounded-xl shadow-sm p-4 space-y-5">
            <input type="hidden" name="q" value="{{ request('q') }}">

            <div>
                <h3 class="font-semibold mb-2 text-sm">Category</h3>
                <select name="category" class="w-full border rounded-lg px-2 py-1.5 text-sm" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <h3 class="font-semibold mb-2 text-sm">Sub-category</h3>
                <select name="sub_category" class="w-full border rounded-lg px-2 py-1.5 text-sm" onchange="this.form.submit()">
                    <option value="">All Sub-categories</option>
                    @foreach($subCategories as $subCategory)
                        <option value="{{ $subCategory->id }}" @selected(request('sub_category') == $subCategory->id)>{{ $subCategory->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <h3 class="font-semibold mb-2 text-sm">Brand</h3>
                <select name="brand" class="w-full border rounded-lg px-2 py-1.5 text-sm" onchange="this.form.submit()">
                    <option value="">All Brands</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}" @selected(request('brand') == $brand->id)>{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <h3 class="font-semibold mb-2 text-sm">Price Range</h3>
                <div class="flex gap-2">
                    <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="w-1/2 border rounded-lg px-2 py-1.5 text-sm">
                    <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="w-1/2 border rounded-lg px-2 py-1.5 text-sm">
                </div>
            </div>

            <div>
                <h3 class="font-semibold mb-2 text-sm">Minimum Rating</h3>
                <select name="min_rating" class="w-full border rounded-lg px-2 py-1.5 text-sm" onchange="this.form.submit()">
                    <option value="">Any Rating</option>
                    @foreach([4,3,2,1] as $r)
                        <option value="{{ $r }}" @selected(request('min_rating') == $r)>{{ $r }}★ & up</option>
                    @endforeach
                </select>
            </div>

            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="in_stock" value="1" @checked(request('in_stock')) onchange="this.form.submit()">
                In stock only
            </label>

            <button class="w-full bg-brand text-white rounded-lg py-2 text-sm font-semibold hover:bg-brand-dark">Apply Filters</button>
            <a href="{{ route('products.index') }}" class="block text-center text-xs text-gray-500 hover:underline">Clear all</a>
        </form>
    </aside>

    <div class="md:col-span-3">
        <div class="flex items-center justify-between mb-4">
            <p class="text-sm text-gray-600">{{ $products->total() }} products found @if(request('q')) for "<strong>{{ request('q') }}</strong>" @endif</p>

            <form method="GET" action="{{ route('products.index') }}">
                @foreach(request()->except('sort') as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <select name="sort" class="border rounded-lg px-2 py-1.5 text-sm" onchange="this.form.submit()">
                    <option value="newest" @selected(request('sort', 'newest') == 'newest')>Newest</option>
                    <option value="price_asc" @selected(request('sort') == 'price_asc')>Price: Low to High</option>
                    <option value="price_desc" @selected(request('sort') == 'price_desc')>Price: High to Low</option>
                    <option value="popularity" @selected(request('sort') == 'popularity')>Popularity</option>
                </select>
            </form>
        </div>

        @if($products->isEmpty())
            <div class="bg-white rounded-xl shadow-sm p-12 text-center text-gray-500">
                <p class="text-lg font-medium mb-1">No products found</p>
                <p class="text-sm">Try adjusting your search or filters.</p>
            </div>
        @else
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>

            <div class="mt-8">{{ $products->links() }}</div>
        @endif
    </div>
</div>
@endsection
