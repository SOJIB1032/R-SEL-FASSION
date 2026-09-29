@extends('layouts.admin')
@section('title', 'Reviews - ShopNova Admin')
@section('page_title', 'Reviews')

@section('content')
<form method="GET" class="flex gap-2 mb-4">
    <select name="status" class="border rounded-lg px-3 py-2 text-sm" onchange="this.form.submit()">
        <option value="">All Statuses</option>
        <option value="pending" @selected(request('status') == 'pending')>Pending</option>
        <option value="approved" @selected(request('status') == 'approved')>Approved</option>
        <option value="rejected" @selected(request('status') == 'rejected')>Rejected</option>
    </select>
</form>

<div class="space-y-4">
    @forelse($reviews as $review)
        <div class="bg-white rounded-xl shadow-sm p-5">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <p class="font-medium text-sm">{{ $review->product->name ?? 'Deleted product' }}</p>
                    <p class="text-xs text-gray-500">by {{ $review->user->name ?? 'Unknown' }} &bull; {{ $review->created_at->format('M d, Y') }}</p>
                </div>
                <span class="text-xs font-semibold px-2 py-1 rounded-full
                    @class([
                        'bg-yellow-100 text-yellow-700' => $review->status === 'pending',
                        'bg-green-100 text-green-700' => $review->status === 'approved',
                        'bg-red-100 text-red-700' => $review->status === 'rejected',
                    ])">{{ ucfirst($review->status) }}</span>
            </div>
            <p class="text-yellow-500 text-sm mb-1">{{ str_repeat('⭐', $review->rating) }}</p>
            <p class="font-medium text-sm">{{ $review->title }}</p>
            <p class="text-sm text-gray-600 mb-3">{{ $review->description }}</p>

            <div class="flex gap-3 text-xs">
                @if($review->status !== 'approved')
                    <form action="{{ route('admin.reviews.approve', $review) }}" method="POST">
                        @csrf
                        <button class="text-green-600 font-medium hover:underline">Approve</button>
                    </form>
                @endif
                @if($review->status !== 'rejected')
                    <form action="{{ route('admin.reviews.reject', $review) }}" method="POST">
                        @csrf
                        <button class="text-yellow-600 font-medium hover:underline">Reject</button>
                    </form>
                @endif
                <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" onsubmit="return confirm('Delete this review?');">
                    @csrf @method('DELETE')
                    <button class="text-red-500 font-medium hover:underline">Delete</button>
                </form>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-xl shadow-sm p-12 text-center text-gray-500">No reviews found.</div>
    @endforelse
</div>
<div class="mt-4">{{ $reviews->links() }}</div>
@endsection
