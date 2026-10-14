<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::query()->active()->with(['primaryImage', 'category', 'brand']);

        if ($search = $request->string('q')->trim()->value()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('category', fn ($categoryQuery) => $categoryQuery->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('brand', fn ($brandQuery) => $brandQuery->where('name', 'like', "%{$search}%"));
            });
        }

        if ($categoryId = $request->integer('category')) {
            $query->where('category_id', $categoryId);
        }

        if ($subCategoryId = $request->integer('sub_category')) {
            $query->where('sub_category_id', $subCategoryId);
        }

        if ($brandId = $request->integer('brand')) {
            $query->where('brand_id', $brandId);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->float('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->float('max_price'));
        }

        if ($request->filled('min_rating')) {
            $query->where('average_rating', '>=', $request->float('min_rating'));
        }

        if ($request->boolean('in_stock')) {
            $query->where('stock_quantity', '>', 0);
        }

        match ($request->string('sort')->value()) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'popularity' => $query->orderByDesc('sales_count'),
            default => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::active()->orderBy('name')->get();
        $brands = Brand::where('is_active', true)->orderBy('name')->get();
        $subCategories = SubCategory::where('is_active', true)->orderBy('name')->get();

        return view('customer.products.index', compact('products', 'categories', 'brands', 'subCategories'));
    }

    public function show(Request $request, Product $product): View
    {
        abort_unless($product->status === 'active', 404);

        $product->load(['images', 'variants' => fn ($q) => $q->where('is_active', true), 'category', 'brand', 'approvedReviews.user']);

        $viewedIds = collect($request->session()->get('recently_viewed_products', []))
            ->reject(fn ($id) => (int) $id === (int) $product->id)
            ->take(7)->values();
        $request->session()->put('recently_viewed_products', collect([$product->id])->merge($viewedIds)->take(8)->all());

        $relatedProducts = Product::active()
            ->with('primaryImage')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        $recentlyViewedProducts = Product::active()
            ->with('primaryImage')
            ->whereIn('id', $viewedIds)
            ->get()
            ->sortBy(fn ($item) => $viewedIds->search($item->id))
            ->values();

        return view('customer.products.show', compact('product', 'relatedProducts', 'recentlyViewedProducts'));
    }
}
