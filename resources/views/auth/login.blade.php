@extends('layouts.app')
@section('title', 'Login - R-SELFASSION')
@section('content')
<div class="max-w-md mx-auto px-4 py-16">
    <div class="bg-white rounded-xl shadow-sm p-8">
        <h1 class="text-2xl font-bold mb-6 text-center">Welcome Back</h1>

        @if ($errors->any())
            <div class="bg-red-50 text-red-700 text-sm rounded-lg p-3 mb-4">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Password</label>
                <input type="password" name="password" required
                       class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand focus:outline-none">
            </div>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="remember"> Remember me
            </label>
            <button type="submit" class="w-full bg-blue-600 text-black rounded-lg py-2.5 font-semibold hover:bg-blue-700">Login</button>
        </form>

        <p class="text-sm text-center mt-6 text-gray-500">
            Don't have an account? <a href="{{ route('register') }}" class="text-brand font-medium hover:underline">Register</a>
        </p>
    </div>
</div>
@endsection
