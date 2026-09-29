<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $order->order_number }} - ShopNova</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print { .no-print { display: none; } body { -webkit-print-color-adjust: exact; } }
    </style>
</head>
<body class="p-10 text-gray-800">
    <div class="no-print mb-6 flex justify-end">
        <button onclick="window.print()" class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm">🖨️ Print</button>
    </div>

    <div class="max-w-3xl mx-auto border rounded-xl p-8">
        <div class="flex justify-between items-start mb-8">
            <div>
                <h1 class="text-2xl font-extrabold text-brand">ShopNova</h1>
                <p class="text-xs text-gray-500">Invoice</p>
            </div>
            <div class="text-right text-sm">
                <p class="font-semibold">{{ $order->order_number }}</p>
                <p class="text-gray-500">{{ $order->created_at->format('M d, Y') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-8 text-sm">
            <div>
                <p class="text-xs text-gray-500 mb-1">Billed To</p>
                <p class="font-medium">{{ $order->full_name }}</p>
                <p>{{ $order->email }}</p>
                <p>{{ $order->phone }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Ship To</p>
                <p>{{ $order->address_line }}</p>
                <p>{{ $order->city }} {{ $order->area }} {{ $order->postal_code }}</p>
            </div>
        </div>

        <table class="w-full text-sm mb-6">
            <thead>
                <tr class="border-b text-left text-gray-500">
                    <th class="py-2">Item</th>
                    <th class="py-2">Qty</th>
                    <th class="py-2">Unit Price</th>
                    <th class="py-2 text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr class="border-b">
                        <td class="py-2">{{ $item->product_name }} @if($item->variant_label)<span class="text-xs text-gray-500">({{ $item->variant_label }})</span>@endif</td>
                        <td class="py-2">{{ $item->quantity }}</td>
                        <td class="py-2">৳{{ number_format($item->unit_price, 2) }}</td>
                        <td class="py-2 text-right">৳{{ number_format($item->line_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="flex justify-end">
            <div class="w-64 space-y-1 text-sm">
                <div class="flex justify-between"><span>Subtotal</span><span>৳{{ number_format($order->subtotal, 2) }}</span></div>
                @if($order->discount_total > 0)<div class="flex justify-between text-green-600"><span>Discount</span><span>-৳{{ number_format($order->discount_total, 2) }}</span></div>@endif
                <div class="flex justify-between"><span>Shipping</span><span>৳{{ number_format($order->shipping_fee, 2) }}</span></div>
                @if($order->tax_total > 0)<div class="flex justify-between"><span>Tax</span><span>৳{{ number_format($order->tax_total, 2) }}</span></div>@endif
                <div class="flex justify-between font-bold text-base border-t pt-2"><span>Total</span><span>৳{{ number_format($order->grand_total, 2) }}</span></div>
            </div>
        </div>

        <div class="mt-10 text-xs text-gray-400 text-center">
            Payment Method: {{ strtoupper($order->payment_method) }} &bull; Payment Status: {{ ucfirst($order->payment_status) }}<br>
            Thank you for shopping with ShopNova.
        </div>
    </div>
</body>
</html>
