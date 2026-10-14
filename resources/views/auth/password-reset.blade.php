@extends('layouts.app')
@section('title', 'Choose a new password - ShopNova')
@section('content')
<div class="max-w-md mx-auto px-4 py-12">
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h1 class="text-2xl font-bold mb-5">Choose a new password</h1>
        @if($errors->any())<div class="text-red-700 bg-red-50 p-3 rounded-lg mb-4">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
        <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <label class="block text-sm">Email<input type="email" name="email" value="{{ old('email', $email) }}" required class="mt-1 w-full border rounded-lg px-3 py-2"></label>
            <label class="block text-sm">New password<input type="password" name="password" required autocomplete="new-password" class="mt-1 w-full border rounded-lg px-3 py-2"></label>
            <label class="block text-sm">Confirm password<input type="password" name="password_confirmation" required autocomplete="new-password" class="mt-1 w-full border rounded-lg px-3 py-2"></label>
            <button class="w-full bg-brand text-white rounded-lg py-2 font-semibold">Reset password</button>
        </form>
    </div>
</div>
@endsection
