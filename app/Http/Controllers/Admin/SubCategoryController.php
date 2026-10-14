<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SubCategoryController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['slug'] = Str::slug($data['name']).'-'.Str::random(4);
        $data['is_active'] = $request->boolean('is_active', true);
        if ($request->hasFile('image')) $data['image'] = $request->file('image')->store('sub-categories', 'public');
        SubCategory::create($data);

        return back()->with('status', 'Sub-category created.');
    }


    public function update(Request $request, SubCategory $subCategory): RedirectResponse
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($subCategory->name !== $data['name']) {
            $data['slug'] = Str::slug($data['name']).'-'.Str::random(4);
        }
        if ($request->hasFile('image')) {
            if ($subCategory->image) Storage::disk('public')->delete($subCategory->image);
            $data['image'] = $request->file('image')->store('sub-categories', 'public');
        }
        $data['is_active'] = $request->boolean('is_active');
        $subCategory->update($data);

        return back()->with('status', 'Sub-category updated.');
    }

    public function toggleActive(SubCategory $subCategory): RedirectResponse
    {
        $subCategory->update(['is_active' => ! $subCategory->is_active]);
        return back()->with('status', 'Sub-category status updated.');
    }

    public function destroy(SubCategory $subCategory): RedirectResponse
    {
        if ($subCategory->image) Storage::disk('public')->delete($subCategory->image);
        $subCategory->delete();

        return back()->with('status', 'Sub-category deleted.');
    }
}
