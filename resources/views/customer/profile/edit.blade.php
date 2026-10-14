@extends('layouts.app')
@section('title', 'My Profile - ShopNova')
@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    @include('customer.partials.account-nav')
    <h1 class="text-2xl font-bold mb-6">My Profile</h1>
    @if(session('status'))
        <div class="bg-green-50 text-green-700 rounded-lg p-3 mb-4">{{ session('status') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-red-50 text-red-700 rounded-lg p-3 mb-4">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif
    <div class="grid md:grid-cols-2 gap-6">
        <form method="POST" action="{{ route('account.profile.update') }}" class="bg-white rounded-xl shadow-sm p-6 space-y-4">
            @csrf @method('PUT')
            <h2 class="font-semibold text-lg">Personal information</h2>
            <label class="block text-sm">Full name<input name="name" value="{{ old('name', $user->name) }}" required maxlength="255" class="mt-1 w-full border rounded-lg px-3 py-2"></label>
            <label class="block text-sm">Email<input type="email" name="email" value="{{ old('email', $user->email) }}" required maxlength="255" class="mt-1 w-full border rounded-lg px-3 py-2"></label>
            <label class="block text-sm">Phone<input name="phone" value="{{ old('phone', $user->phone) }}" maxlength="30" class="mt-1 w-full border rounded-lg px-3 py-2"></label>
            <button class="bg-brand text-white rounded-lg px-4 py-2 font-semibold">Save profile</button>
        </form>
        <form method="POST" action="{{ route('account.password.update') }}" class="bg-white rounded-xl shadow-sm p-6 space-y-4">
            @csrf @method('PUT')
            <h2 class="font-semibold text-lg">Change password</h2>
            <label class="block text-sm">Current password<input type="password" name="current_password" required autocomplete="current-password" class="mt-1 w-full border rounded-lg px-3 py-2"></label>
            <label class="block text-sm">New password<input type="password" name="password" required autocomplete="new-password" class="mt-1 w-full border rounded-lg px-3 py-2"></label>
            <label class="block text-sm">Confirm new password<input type="password" name="password_confirmation" required autocomplete="new-password" class="mt-1 w-full border rounded-lg px-3 py-2"></label>
            <p class="text-xs text-gray-500">Use at least 8 characters, including uppercase, lowercase and numbers.</p>
            <button class="bg-brand text-white rounded-lg px-4 py-2 font-semibold">Change password</button>
        </form>
    </div>
</div>
@endsection
