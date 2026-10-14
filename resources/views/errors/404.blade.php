@extends('layouts.app')
@section('title', '404 — Page not found | ShopNova')
@section('content')
<div class="max-w-xl mx-auto px-4 py-20 text-center">
    <p class="text-brand font-extrabold text-5xl mb-4">404</p>
    <h1 class="text-2xl font-bold mb-3">Page not found</h1>
    <p class="text-gray-600 mb-8">The page you are looking for may have moved or no longer exists.</p>
    <a href="{{ route('home') }}" class="inline-block bg-brand text-white rounded-lg px-5 py-2.5 font-semibold">Back to ShopNova</a>
</div>
@endsection
