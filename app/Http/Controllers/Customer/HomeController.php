<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featured = Product::active()->where('is_featured', true)->with('primaryImage')->limit(8)->get();
        $newArrivals = Product::active()->where('is_new_arrival', true)->with('primaryImage')->limit(8)->get();
        $bestSellers = Product::active()->where('is_best_seller', true)->with('primaryImage')->limit(8)->get();
        $onSale = Product::active()->whereNotNull('discount_price')->with('primaryImage')->limit(8)->get();
        $categories = Category::active()->orderBy('sort_order')->limit(8)->get();

        return view('customer.home', compact('featured', 'newArrivals', 'bestSellers', 'onSale', 'categories'));
    }
}
