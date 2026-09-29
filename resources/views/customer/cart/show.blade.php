@extends('layouts.app')
@section('title', 'Your Cart - ShopNova')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Your Cart</h1>

    @if ($errors->any())
        <div class="bg-red-50 text-red-700 text-sm rounded-lg p-3 mb-4">
            @foreach ($errors->all() as $error) <p>{{ $error }}</p> @endforeach
        </div>
    @endif

    @if($cart->items->isEmpty())
        <div class="bg-white rounded-xl shadow-sm p-12 text-center text-gray-500">
            <p class="text-lg font-medium mb-2">Your cart is empty</p>
            <a href="{{ route('products.index') }}" class="text-brand font-medium hover:underline">Start shopping →</a>
        </div>
    @else
        <div class="grid md:grid-cols-3 gap-8">
            <div class="md:col-span-2 space-y-4">
                @foreach($cart->items as $item)
                    <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-4">
                        <div class="w-20 h-20 bg-gray-100 rounded-lg overflow-hidden shrink-0">
                            @if($item->product->primaryImage)
                                <img src="{{ asset('storage/'.$item->product->primaryImage->path) }}" class="object-cover w-full h-full">
                            @endif
                        </div>
                        <div class="flex-1">
                            <a href="{{ route('products.show', $item->product) }}" class="font-medium hover:text-brand">{{ $item->product->name }}</a>
                            @if($item->variant)
                                <p class="text-xs text-gray-500">{{ $item->variant->label }}</p>
                            @endif
                            <p class="text-brand font-semibold mt-1">৳{{ number_format($item->unit_price, 2) }}</p>
                        </div>

                        <form action="{{ route('cart.items.update', $item) }}" method="POST" class="flex items-center gap-2">
                            @csrf @method('PATCH')
                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" class="w-16 border rounded-lg px-2 py-1 text-sm" onchange="this.form.submit()">
                        </form>

                        <p class="w-24 text-right font-semibold">৳{{ number_format($item->line_total, 2) }}</p>

                        <form action="{{ route('cart.items.destroy', $item) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-gray-400 hover:text-red-500" title="Remove">✕</button>
                        </form>
                    </div>
                @endforeach
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 h-fit">
                <h2 class="font-semibold mb-4">Order Summary</h2>

                @if($cart->coupon_code)
                    <div class="flex items-center justify-between text-sm mb-2 bg-green-50 text-green-700 rounded-lg px-3 py-2">
                        <span>Coupon "{{ $cart->coupon_code }}" applied</span>
                        <form action="{{ route('cart.coupon.remove') }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="underline">Remove</button>
                        </form>
                    </div>
                @else
                    <form action="{{ route('cart.coupon.apply') }}" method="POST" class="flex gap-2 mb-4">
                        @csrf
                        <input type="text" name="code" placeholder="Coupon code" class="flex-1 border rounded-lg px-3 py-2 text-sm">
                        <button class="bg-gray-800 text-white rounded-lg px-3 text-sm">Apply</button>
                    </form>
                @endif

                <div class="space-y-2 text-sm border-t pt-4">
                    <div class="flex justify-between"><span>Subtotal</span><span>৳{{ number_format($cart->subtotal, 2) }}</span></div>
                    @if($cart->coupon_discount > 0)
                        <div class="flex justify-between text-green-600"><span>Discount</span><span>-৳{{ number_format($cart->coupon_discount, 2) }}</span></div>
                    @endif
                    <div class="flex justify-between text-gray-500"><span>Shipping &amp; tax</span><span>Calculated at checkout</span></div>
                    <div class="flex justify-between font-bold text-base border-t pt-2"><span>Estimated Total</span><span>৳{{ number_format($cart->grand_total, 2) }}</span></div>
                </div>

                <a href="{{ route('checkout.show') }}" class="block text-center bg-brand text-white rounded-lg py-2.5 font-semibold mt-4 hover:bg-brand-dark">
                    Proceed to Checkout
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
