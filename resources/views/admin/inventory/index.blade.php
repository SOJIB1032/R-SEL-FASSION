@extends('layouts.admin')
@section('title', 'Inventory - ShopNova Admin')
@section('page_title', 'Inventory Management')
@section('content')
<div class="grid xl:grid-cols-3 gap-6">
    <section class="xl:col-span-2 bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-5 border-b">
            <h2 class="font-semibold text-lg">Stock history</h2>
            <p class="text-sm text-gray-500">Every adjustment and order stock movement is recorded.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left text-gray-500"><tr>
                    <th class="px-4 py-3">Date</th><th class="px-4 py-3">Product / Variant</th>
                    <th class="px-4 py-3">Movement</th><th class="px-4 py-3">Reason</th><th class="px-4 py-3">By</th>
                </tr></thead>
                <tbody class="divide-y">
                @forelse($transactions as $transaction)
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">{{ $transaction->created_at->format('d M Y H:i') }}</td>
                        <td class="px-4 py-3">{{ $transaction->product?->name ?? 'Deleted product' }} @if($transaction->variant)<span class="block text-xs text-gray-500">{{ $transaction->variant->label }}</span>@endif</td>
                        <td class="px-4 py-3"><span class="rounded-full px-2 py-1 text-xs {{ $transaction->type === 'increase' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ ucfirst($transaction->type) }} {{ abs($transaction->quantity) }}</span></td>
                        <td class="px-4 py-3">{{ $transaction->reason ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $transaction->creator?->name ?? 'System' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">No stock movements recorded yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $transactions->links() }}</div>
    </section>
    <aside class="space-y-6">
        <form action="{{ route('admin.inventory.adjust') }}" method="POST" class="bg-white rounded-xl shadow-sm p-5 space-y-3">
            @csrf
            <h2 class="font-semibold text-lg">Adjust stock</h2>
            @if($errors->any())<div class="text-sm text-red-700 bg-red-50 rounded p-3">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
            <label class="block text-sm">Product
                <select name="product_id" required class="mt-1 w-full border rounded-lg p-2" id="inventory-product">
                    <option value="">Select product</option>
                    @foreach($products as $product)<option value="{{ $product->id }}" @selected(old('product_id') == $product->id)>{{ $product->name }} ({{ $product->stock_quantity }} in stock)</option>@endforeach
                </select>
            </label>
            <label class="block text-sm">Variant (optional)
                <select name="product_variant_id" class="mt-1 w-full border rounded-lg p-2" id="inventory-variant">
                    <option value="">No variant</option>
                    @foreach($products as $product)
                        @foreach($product->variants as $variant)
                            <option value="{{ $variant->id }}" data-product="{{ $product->id }}" @selected(old('product_variant_id') == $variant->id)>{{ $product->name }} — {{ $variant->label }} ({{ $variant->stock_quantity }})</option>
                        @endforeach
                    @endforeach
                </select>
            </label>
            <label class="block text-sm">Movement
                <select name="type" required class="mt-1 w-full border rounded-lg p-2"><option value="increase">Increase stock</option><option value="decrease">Decrease stock</option></select>
            </label>
            <label class="block text-sm">Quantity<input type="number" name="quantity" min="1" max="100000" required value="{{ old('quantity', 1) }}" class="mt-1 w-full border rounded-lg p-2"></label>
            <label class="block text-sm">Reason<input name="reason" maxlength="255" required value="{{ old('reason') }}" placeholder="e.g. Supplier delivery / damaged goods" class="mt-1 w-full border rounded-lg p-2"></label>
            <button class="w-full bg-brand text-white rounded-lg py-2 font-semibold">Save adjustment</button>
        </form>
        <div class="bg-white rounded-xl shadow-sm p-5">
            <h2 class="font-semibold text-lg mb-3">Low / out of stock</h2>
            @forelse($lowStockProducts as $product)
                <div class="flex justify-between gap-3 py-2 border-b last:border-0 text-sm">
                    <span>{{ $product->name }}</span>
                    <span class="{{ $product->stock_quantity === 0 ? 'text-red-600' : 'text-amber-600' }} font-semibold">{{ $product->stock_quantity }} left</span>
                </div>
            @empty
                <p class="text-sm text-gray-500">No low-stock products.</p>
            @endforelse
        </div>
    </aside>
</div>
<script>
const productSelect = document.getElementById('inventory-product');
const variantSelect = document.getElementById('inventory-variant');
function filterInventoryVariants() {
    [...variantSelect.options].forEach((option) => {
        if (!option.value) return;
        option.hidden = option.dataset.product !== productSelect.value;
        if (option.hidden && option.selected) variantSelect.value = '';
    });
}
productSelect.addEventListener('change', filterInventoryVariants);
filterInventoryVariants();
</script>
@endsection
