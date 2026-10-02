@extends('layouts.app')
@section('title', 'Checkout - R-SELFASSION')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Checkout</h1>

    @if ($errors->any())
        <div class="bg-red-50 text-red-700 text-sm rounded-lg p-3 mb-4">
            @foreach ($errors->all() as $error) <p>{{ $error }}</p> @endforeach
        </div>
    @endif

    <form action="{{ route('checkout.store') }}" method="POST" class="grid md:grid-cols-3 gap-8">
        @csrf

        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold">Shipping Details</h2>
                    @if($addresses->isNotEmpty())
                        <select id="address-select" class="border rounded-lg px-2 py-1.5 text-xs">
                            <option value="">Use a saved address...</option>
                            @foreach($addresses as $address)
                                <option value="{{ $address->id }}"
                                    data-full_name="{{ $address->full_name }}"
                                    data-phone="{{ $address->phone }}"
                                    data-address_line="{{ $address->address_line }}"
                                    data-city="{{ $address->city }}"
                                    data-area="{{ $address->area }}"
                                    data-postal_code="{{ $address->postal_code }}">
                                    {{ $address->full_name }} — {{ $address->city }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium mb-1">Full Name</label>
                        <input type="text" name="full_name" value="{{ old('full_name', auth()->user()->name) }}" required class="w-full border rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required class="w-full border rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone) }}" required class="w-full border rounded-lg px-3 py-2">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium mb-1">Address</label>
                        <input type="text" name="address_line" value="{{ old('address_line') }}" required class="w-full border rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">City</label>
                        <input type="text" name="city" value="{{ old('city') }}" required class="w-full border rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Area</label>
                        <input type="text" name="area" value="{{ old('area') }}" class="w-full border rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Postal Code</label>
                        <input type="text" name="postal_code" value="{{ old('postal_code') }}" class="w-full border rounded-lg px-3 py-2">
                    </div>
                </div>

                <label class="flex items-center gap-2 text-sm mt-4">
                    <input type="checkbox" name="save_address" value="1"> Save this address for next time
                </label>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="font-semibold mb-4">Shipping Method</h2>
                <div class="space-y-2">
                    <label class="flex items-center gap-3 border rounded-lg p-3 cursor-pointer">
                        <input type="radio" name="shipping_method" value="standard" checked> Standard (3–5 days) — ৳{{ number_format(config('shopnova.flat_shipping_fee'), 2) }}
                    </label>
                    <label class="flex items-center gap-3 border rounded-lg p-3 cursor-pointer">
                        <input type="radio" name="shipping_method" value="express"> Express (1–2 days) — ৳{{ number_format(config('shopnova.flat_shipping_fee') * 2, 2) }}
                    </label>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="font-semibold mb-4">Payment Method</h2>
                <div class="space-y-2">
                    <label class="flex items-center gap-3 border rounded-lg p-3 cursor-pointer bg-orange-50 border-brand">
                        <input type="radio" name="payment_method" value="cod" checked> Cash on Delivery
                    </label>
                    @foreach(['bkash' => 'bKash', 'nagad' => 'Nagad', 'sslcommerz' => 'SSLCommerz'] as $value => $label)
                        <label class="flex items-center gap-3 border rounded-lg p-3 text-gray-400">
                            <input type="radio" name="payment_method" value="{{ $value }}" disabled> {{ $label }} <span class="text-xs">(coming soon)</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <label class="block text-sm font-medium mb-1">Order Notes (optional)</label>
                <textarea name="order_notes" rows="2" class="w-full border rounded-lg px-3 py-2">{{ old('order_notes') }}</textarea>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 h-fit">
            <h2 class="font-semibold mb-4">Order Summary</h2>
            <div class="space-y-2 text-sm max-h-64 overflow-y-auto mb-4">
                @foreach($cart->items as $item)
                    <div class="flex justify-between">
                        <span>{{ $item->product->name }} × {{ $item->quantity }}</span>
                        <span>৳{{ number_format($item->line_total, 2) }}</span>
                    </div>
                @endforeach
            </div>
            <div class="space-y-2 text-sm border-t pt-4">
                <div class="flex justify-between"><span>Subtotal</span><span>৳{{ number_format($cart->subtotal, 2) }}</span></div>
                @if($cart->coupon_discount > 0)
                    <div class="flex justify-between text-green-600"><span>Discount ({{ $cart->coupon_code }})</span><span>-৳{{ number_format($cart->coupon_discount, 2) }}</span></div>
                @endif
                <div class="flex justify-between"><span>Shipping</span><span>৳{{ number_format(config('shopnova.flat_shipping_fee'), 2) }}+</span></div>
                <div class="flex justify-between font-bold text-base border-t pt-2"><span>Total</span><span>Calculated on submit</span></div>
            </div>

            <button type="submit" class="w-full bg-brand text-white rounded-lg py-2.5 font-semibold mt-4 hover:bg-brand-dark">
                Place Order
            </button>
            <p class="text-xs text-gray-400 mt-2">Prices, stock, and your coupon are re-verified server-side when you place the order.</p>
        </div>
    </form>
</div>

<script>
    const select = document.getElementById('address-select');
    if (select) {
        select.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            if (!opt.value) return;
            ['full_name', 'phone', 'address_line', 'city', 'area', 'postal_code'].forEach((field) => {
                const input = document.querySelector(`[name="${field}"]`);
                if (input) input.value = opt.dataset[field] || '';
            });
        });
    }
</script>
@endsection
