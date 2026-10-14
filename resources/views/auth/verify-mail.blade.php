@extends('layouts.app')
@section('title', 'Verify email - ShopNova')
@section('content')
<div class="max-w-lg mx-auto px-4 py-12">
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h1 class="text-2xl font-bold mb-2">Verify your email</h1>
        <p class="text-gray-600 text-sm mb-5">Use the verification link sent to your email address. Email delivery requires valid MAIL settings in your .env file.</p>
        @if(session('status'))<p class="text-green-700 bg-green-50 p-3 rounded-lg mb-4">{{ session('status') }}</p>@endif
        <form method="POST" action="{{ route('verification.send') }}">@csrf<button class="bg-brand text-white rounded-lg px-4 py-2 font-semibold">Resend verification email</button></form>
    </div>
</div>
@endsection
