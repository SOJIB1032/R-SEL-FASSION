@extends('layouts.app')
@section('title', '500 — Something went wrong | ShopNova')
@section('content')
<div class="max-w-xl mx-auto px-4 py-20 text-center">
    <p class="text-brand font-extrabold text-5xl mb-4">500</p>
    <h1 class="text-2xl font-bold mb-3">Something went wrong</h1>
    <p class="text-gray-600 mb-8">An unexpected error occurred. Please try again in a moment.</p>
    <a href="{{ route('home') }}" class="inline-block bg-brand text-white rounded-lg px-5 py-2.5 font-semibold">Back to ShopNova</a>
</div>
@endsection
