@extends('layouts.app')
@section('title', '419 — Page expired | ShopNova')
@section('content')
<div class="max-w-xl mx-auto px-4 py-20 text-center">
    <p class="text-brand font-extrabold text-5xl mb-4">419</p>
    <h1 class="text-2xl font-bold mb-3">Page expired</h1>
    <p class="text-gray-600 mb-8">Your session expired. Please go back, refresh the page and try again.</p>
    <a href="{{ route('home') }}" class="inline-block bg-brand text-white rounded-lg px-5 py-2.5 font-semibold">Back to ShopNova</a>
</div>
@endsection
