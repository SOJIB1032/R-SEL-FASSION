<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubCategoryController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        $data['slug'] = Str::slug($data['name']).'-'.Str::random(4);
        SubCategory::create($data);

        return back()->with('status', 'Sub-category created.');
    }

    public function destroy(SubCategory $subCategory): RedirectResponse
    {
        $subCategory->delete();

        return back()->with('status', 'Sub-category deleted.');
    }
}
