@extends('layouts.admin')
@section('title', 'Customers - ShopNova Admin')
@section('page_title', 'Customers')

@section('content')
<form method="GET" class="flex gap-2 mb-4">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name, email, phone..." class="border rounded-lg px-3 py-2 text-sm w-80">
    <button class="bg-gray-800 text-white rounded-lg px-4 text-sm">Search</button>
</form>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-500">
            <tr>
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3">Email</th>
                <th class="px-4 py-3">Orders</th>
                <th class="px-4 py-3">Joined</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3 text-right">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @foreach($customers as $customer)
                <tr>
                    <td class="px-4 py-3 font-medium">{{ $customer->name }}</td>
                    <td class="px-4 py-3">{{ $customer->email }}</td>
                    <td class="px-4 py-3">{{ $customer->orders_count }}</td>
                    <td class="px-4 py-3">{{ $customer->created_at->format('M d, Y') }}</td>
                    <td class="px-4 py-3">
                        <form action="{{ route('admin.customers.toggle-active', $customer) }}" method="POST">
                            @csrf
                            <button class="text-xs font-semibold px-2 py-1 rounded-full {{ $customer->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $customer->is_active ? 'Active' : 'Disabled' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.customers.show', $customer) }}" class="text-brand font-medium hover:underline">View</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $customers->links() }}</div>
@endsection
