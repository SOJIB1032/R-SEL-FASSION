<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BrandController extends Controller
{
    public function index(): View
    {
        $brands = Brand::withCount('products')->orderBy('name')->paginate(15);

        return view('admin.brands.index', compact('brands'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        $payload = ['name' => $data['name'], 'slug' => Str::slug($data['name']).'-'.Str::random(4), 'is_active' => true];

        if ($request->hasFile('logo')) {
            $payload['logo'] = $request->file('logo')->store('brands', 'public');
        }

        Brand::create($payload);

        return back()->with('status', 'Brand created.');
    }

    public function toggleActive(Brand $brand): RedirectResponse
    {
        $brand->update(['is_active' => ! $brand->is_active]);

        return back()->with('status', 'Brand status updated.');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        if ($brand->products()->exists()) {
            return back()->withErrors(['brand' => 'Cannot delete a brand that still has products.']);
        }

        if ($brand->logo) {
            Storage::disk('public')->delete($brand->logo);
        }
        $brand->delete();

        return back()->with('status', 'Brand deleted.');
    }
}
