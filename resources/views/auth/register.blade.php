@extends('layouts.app')
@section('title', 'Create Account - ShopNova')
@section('content')
<div class="max-w-md mx-auto px-4 py-16">
    <div class="bg-white rounded-xl shadow-sm p-8">
        <h1 class="text-2xl font-bold mb-6 text-center">Create Your Account</h1>

        @if ($errors->any())
            <div class="bg-red-50 text-red-700 text-sm rounded-lg p-3 mb-4">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">Full Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus
                       class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Phone (optional)</label>
                <input type="text" name="phone" value="{{ old('phone') }}"
                       class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Password</label>
                <input type="password" name="password" required
                       class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation" required
                       class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand focus:outline-none">
            </div>
            <button type="submit" class="w-full bg-brand text-white rounded-lg py-2.5 font-semibold hover:bg-brand-dark">Create Account</button>
        </form>

        <p class="text-sm text-center mt-6 text-gray-500">
            Already have an account? <a href="{{ route('login') }}" class="text-brand font-medium hover:underline">Login</a>
        </p>
    </div>
</div>
@endsection
