@extends('layouts.app')
@section('title', 'Reset password - ShopNova')
@section('content')
<div class="max-w-md mx-auto px-4 py-12">
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h1 class="text-2xl font-bold mb-2">Forgot password?</h1>
        <p class="text-sm text-gray-500 mb-5">Enter your email address and we will send a reset link if the account exists.</p>
        @if(session('status'))<p class="text-green-700 bg-green-50 p-3 rounded-lg mb-4">{{ session('status') }}</p>@endif
        @error('email')<p class="text-red-700 text-sm mb-3">{{ $message }}</p>@enderror
        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf
            <label class="block text-sm">Email address<input type="email" name="email" value="{{ old('email') }}" required class="mt-1 w-full border rounded-lg px-3 py-2"></label>
            <button class="w-full bg-brand text-white rounded-lg py-2 font-semibold">Send reset link</button>
        </form>
    </div>
</div>
@endsection
