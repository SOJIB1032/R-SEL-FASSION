<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $query = Review::with('product', 'user');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $reviews = $query->latest()->paginate(15)->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function approve(Review $review): RedirectResponse
    {
        $review->update(['status' => 'approved']);
        $this->recalculateProductRating($review);

        return back()->with('status', 'Review approved.');
    }

    public function reject(Review $review): RedirectResponse
    {
        $review->update(['status' => 'rejected']);
        $this->recalculateProductRating($review);

        return back()->with('status', 'Review rejected.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();
        $this->recalculateProductRating($review);

        return back()->with('status', 'Review deleted.');
    }

    protected function recalculateProductRating(Review $review): void
    {
        $product = $review->product;
        $approved = $product->approvedReviews();

        $product->update([
            'average_rating' => $approved->avg('rating') ?? 0,
            'review_count' => $approved->count(),
        ]);
    }
}
