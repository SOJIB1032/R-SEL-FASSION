<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::with('variants')->orderBy('name')->get();
        $transactions = InventoryTransaction::with('product', 'variant', 'creator')
            ->latest()->paginate(20)->withQueryString();
        $lowStockProducts = Product::whereColumn('stock_quantity', '<=', 'low_stock_threshold')->get();

        return view('admin.inventory.index', compact('products', 'transactions', 'lowStockProducts'));
    }

    public function adjust(Request $request, InventoryService $inventory): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'product_variant_id' => ['nullable', 'exists:product_variants,id'],
            'type' => ['required', 'in:increase,decrease'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100000'],
            'reason' => ['required', 'string', 'max:255'],
        ]);

        try {
            DB::transaction(function () use ($data, $request, $inventory) {
                $product = Product::whereKey($data['product_id'])->lockForUpdate()->firstOrFail();
                $variant = null;

                if (! empty($data['product_variant_id'])) {
                    $variant = ProductVariant::whereKey($data['product_variant_id'])
                        ->where('product_id', $product->id)
                        ->lockForUpdate()->first();

                    if (! $variant) {
                        throw ValidationException::withMessages(['product_variant_id' => 'Choose a variant belonging to the selected product.']);
                    }
                }

                $quantity = (int) $data['quantity'];
                if ($data['type'] === 'decrease') {
                    $available = $variant ? min($variant->stock_quantity, $product->stock_quantity) : $product->stock_quantity;
                    if ($quantity > $available) {
                        throw ValidationException::withMessages(['quantity' => "Only {$available} unit(s) are available."]);
                    }
                    $inventory->decrease($product, $variant, $quantity, null, $request->user()->id, $data['reason']);
                } else {
                    $inventory->increase($product, $variant, $quantity, null, $request->user()->id, $data['reason']);
                }

            }, 3);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        return back()->with('status', 'Inventory adjusted successfully.');
    }
}
