@extends('layouts.app')
@section('title', 'Track Order '.$order->order_number.' - ShopNova')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    @include('customer.partials.account-nav')

    <h1 class="text-2xl font-bold mb-1">Track Order {{ $order->order_number }}</h1>
    <p class="text-sm text-gray-500 mb-8">Placed on {{ $order->created_at->format('M d, Y') }}</p>

    @if($order->status === 'cancelled')
        <div class="bg-red-50 text-red-700 rounded-xl p-6 text-center font-medium">
            This order was cancelled on {{ $order->cancelled_at?->format('M d, Y') }}.
        </div>
    @elseif($order->status === 'returned')
        <div class="bg-gray-100 text-gray-700 rounded-xl p-6 text-center font-medium">
            This order was returned.
        </div>
    @else
        <div class="flex items-center">
            @foreach($steps as $i => $step)
                <div class="flex-1 flex flex-col items-center relative">
                    @if($i > 0)
                        <div class="absolute top-4 right-1/2 w-full h-0.5 {{ $i <= $currentIndex ? 'bg-brand' : 'bg-gray-200' }}"></div>
                    @endif
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-bold relative z-10 {{ $i <= $currentIndex ? 'bg-brand' : 'bg-gray-300' }}">
                        {{ $i + 1 }}
                    </div>
                    <p class="text-xs mt-2 text-center font-medium capitalize {{ $i <= $currentIndex ? 'text-gray-800' : 'text-gray-400' }}">{{ $step }}</p>
                </div>
            @endforeach
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 mt-10 text-sm space-y-2">
            @if($order->confirmed_at)<p><strong>Confirmed:</strong> {{ $order->confirmed_at->format('M d, Y g:i A') }}</p>@endif
            @if($order->shipped_at)<p><strong>Shipped:</strong> {{ $order->shipped_at->format('M d, Y g:i A') }}</p>@endif
            @if($order->delivered_at)<p><strong>Delivered:</strong> {{ $order->delivered_at->format('M d, Y g:i A') }}</p>@endif
            @if(! $order->confirmed_at)<p class="text-gray-500">Your order is being reviewed and will be confirmed shortly.</p>@endif
        </div>
    @endif

    <a href="{{ route('account.orders.show', $order) }}" class="inline-block mt-8 text-brand text-sm font-medium hover:underline">← Back to order details</a>
</div>
@endsection
