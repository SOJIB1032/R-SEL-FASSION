<div class="flex flex-wrap gap-2 mb-6 text-sm font-medium">
    <a href="{{ route('account.dashboard') }}" class="px-4 py-2 rounded-lg {{ request()->routeIs('account.dashboard') ? 'bg-brand text-white' : 'bg-white hover:bg-gray-50' }}">Dashboard</a>
    <a href="{{ route('account.orders.index') }}" class="px-4 py-2 rounded-lg {{ request()->routeIs('account.orders.*') ? 'bg-brand text-white' : 'bg-white hover:bg-gray-50' }}">My Orders</a>
    <a href="{{ route('wishlist.show') }}" class="px-4 py-2 rounded-lg {{ request()->routeIs('wishlist.*') ? 'bg-brand text-white' : 'bg-white hover:bg-gray-50' }}">Wishlist</a>
    <a href="{{ route('account.addresses.index') }}" class="px-4 py-2 rounded-lg {{ request()->routeIs('account.addresses.*') ? 'bg-brand text-white' : 'bg-white hover:bg-gray-50' }}">Saved Addresses</a>
</div>
