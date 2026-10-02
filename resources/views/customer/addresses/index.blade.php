@extends('layouts.app')
@section('title', 'Saved Addresses - R-SELFASSION')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    @include('customer.partials.account-nav')

    <h1 class="text-2xl font-bold mb-6">Saved Addresses</h1>

    @if (session('status'))
        <div class="bg-green-50 text-green-700 text-sm rounded-lg p-3 mb-4">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="bg-red-50 text-red-700 text-sm rounded-lg p-3 mb-4">
            @foreach ($errors->all() as $error) <p>{{ $error }}</p> @endforeach
        </div>
    @endif

    <div class="grid md:grid-cols-2 gap-6">
        <div class="space-y-4">
            @forelse($addresses as $address)
                <div class="bg-white rounded-xl shadow-sm p-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-semibold">{{ $address->full_name }} @if($address->is_default)<span class="text-xs bg-brand text-white rounded-full px-2 py-0.5 ml-1">Default</span>@endif</p>
                            <p class="text-sm text-gray-600">{{ $address->phone }}</p>
                            <p class="text-sm text-gray-600">{{ $address->address_line }}, {{ $address->city }} {{ $address->postal_code }}</p>
                        </div>
                    </div>
                    <div class="flex gap-3 mt-3 text-xs">
                        @unless($address->is_default)
                            <form action="{{ route('account.addresses.default', $address) }}" method="POST">
                                @csrf
                                <button class="text-brand font-medium hover:underline">Make default</button>
                            </form>
                        @endunless
                        <form action="{{ route('account.addresses.destroy', $address) }}" method="POST" onsubmit="return confirm('Delete this address?');">
                            @csrf @method('DELETE')
                            <button class="text-red-500 font-medium hover:underline">Delete</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-sm p-8 text-center text-gray-500 text-sm">No saved addresses yet.</div>
            @endforelse
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 h-fit">
            <h2 class="font-semibold mb-4">Add New Address</h2>
            <form action="{{ route('account.addresses.store') }}" method="POST" class="space-y-3">
                @csrf
                <input type="text" name="full_name" placeholder="Full Name" required class="w-full border rounded-lg px-3 py-2 text-sm">
                <input type="text" name="phone" placeholder="Phone" required class="w-full border rounded-lg px-3 py-2 text-sm">
                <input type="text" name="address_line" placeholder="Address" required class="w-full border rounded-lg px-3 py-2 text-sm">
                <div class="grid grid-cols-2 gap-3">
                    <input type="text" name="city" placeholder="City" required class="w-full border rounded-lg px-3 py-2 text-sm">
                    <input type="text" name="area" placeholder="Area" class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
                <input type="text" name="postal_code" placeholder="Postal Code" class="w-full border rounded-lg px-3 py-2 text-sm">
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="is_default" value="1"> Set as default
                </label>
                <button class="w-full bg-brand text-white rounded-lg py-2.5 font-semibold hover:bg-brand-dark">Save Address</button>
            </form>
        </div>
    </div>
</div>
@endsection
