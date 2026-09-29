<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\SubCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::query()->with(['category', 'brand', 'primaryImage']);

        if ($search = $request->string('q')->trim()->value()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->integer('category'));
        }

        if ($request->boolean('low_stock')) {
            $query->whereColumn('stock_quantity', '<=', 'low_stock_threshold');
        }

        $products = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        return view('admin.products.form', [
            'product' => new Product,
            'categories' => Category::orderBy('name')->get(),
            'subCategories' => SubCategory::orderBy('name')->get(),
            'brands' => Brand::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $product = DB::transaction(function () use ($data, $request) {
            $product = Product::create($data);
            $this->syncImages($request, $product);
            $this->syncVariants($request, $product);

            return $product;
        });

        return redirect()->route('admin.products.edit', $product)->with('status', 'Product created.');
    }

    public function edit(Product $product): View
    {
        $product->load('images', 'variants');

        return view('admin.products.form', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
            'subCategories' => SubCategory::orderBy('name')->get(),
            'brands' => Brand::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validated($request, $product->id);

        DB::transaction(function () use ($data, $request, $product) {
            $product->update($data);
            $this->syncImages($request, $product);
            $this->syncVariants($request, $product);
        });

        return redirect()->route('admin.products.edit', $product)->with('status', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->path);
        }
        $product->delete();

        return redirect()->route('admin.products.index')->with('status', 'Product deleted.');
    }

    public function toggleStatus(Product $product): RedirectResponse
    {
        $product->update(['status' => $product->status === 'active' ? 'inactive' : 'active']);

        return back()->with('status', 'Product status updated.');
    }

    public function deleteImage(ProductImage $productImage): RedirectResponse
    {
        Storage::disk('public')->delete($productImage->path);
        $wasPrimary = $productImage->is_primary;
        $productId = $productImage->product_id;
        $productImage->delete();

        if ($wasPrimary) {
            ProductImage::where('product_id', $productId)->first()?->update(['is_primary' => true]);
        }

        return back()->with('status', 'Image removed.');
    }

    protected function validated(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:100', 'unique:products,sku,'.($ignoreId ?? 'NULL').',id'],
            'category_id' => ['required', 'exists:categories,id'],
            'sub_category_id' => ['nullable', 'exists:sub_categories,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'dimensions' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:active,inactive,draft'],
            'is_featured' => ['nullable', 'boolean'],
            'is_new_arrival' => ['nullable', 'boolean'],
            'is_best_seller' => ['nullable', 'boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:255'],
        ]);

        $existing = $ignoreId ? Product::find($ignoreId) : null;
        if (! $existing || $existing->name !== $data['name']) {
            $data['slug'] = Str::slug($data['name']).'-'.Str::random(6);
        }

        foreach (['is_featured', 'is_new_arrival', 'is_best_seller'] as $flag) {
            $data[$flag] = $request->boolean($flag);
        }
        $data['low_stock_threshold'] = $data['low_stock_threshold'] ?? 5;

        return $data;
    }

    protected function syncImages(Request $request, Product $product): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $hasPrimary = $product->images()->where('is_primary', true)->exists();

        foreach ($request->file('images') as $index => $file) {
            $path = $file->store('products', 'public');
            $product->images()->create([
                'path' => $path,
                'is_primary' => ! $hasPrimary && $index === 0,
                'sort_order' => $product->images()->count(),
            ]);
        }
    }

    protected function syncVariants(Request $request, Product $product): void
    {
        $variants = $request->input('variants', []);
        $keptIds = [];

        foreach ($variants as $variant) {
            if (empty($variant['size']) && empty($variant['color'])) {
                continue;
            }

            $payload = [
                'size' => $variant['size'] ?? null,
                'color' => $variant['color'] ?? null,
                'sku' => $variant['sku'] ?: $product->sku.'-'.Str::upper(Str::random(4)),
                'price_adjustment' => $variant['price_adjustment'] ?? 0,
                'stock_quantity' => $variant['stock_quantity'] ?? 0,
                'is_active' => true,
            ];

            if (! empty($variant['id'])) {
                $existing = ProductVariant::where('id', $variant['id'])->where('product_id', $product->id)->first();
                if ($existing) {
                    $existing->update($payload);
                    $keptIds[] = $existing->id;

                    continue;
                }
            }

            $created = $product->variants()->create($payload);
            $keptIds[] = $created->id;
        }

        $product->variants()->whereNotIn('id', $keptIds)->delete();
    }
}
