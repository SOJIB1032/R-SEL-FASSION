<header class="bg-white shadow-sm sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between h-16 gap-4">
            <a href="{{ route('home') }}" class="text-2xl font-extrabold text-brand tracking-tight">R-SEL<span class="text-blue-900">FASSION</span></a>

            <form action="{{ route('products.index') }}" method="GET" class="hidden md:flex flex-1 max-w-xl">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search products, brands..."
                       class="w-full border border-gray-300 rounded-l-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-brand-500">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-black px-4 rounded-r-lg">Search</button>
            </form>

            <nav class="flex items-center gap-5 text-sm font-medium">
                <a href="{{ route('products.index') }}" class="hover:text-brand">Shop</a>

                @auth
                    <a href="{{ route('wishlist.show') }}" class="relative hover:text-brand" title="Wishlist">
                        ♥ Wishlist
                        @if($wishlistCount > 0)
                            <span class="absolute -top-2 -right-3 bg-brand-600 text-black text-[10px] rounded-full w-4 h-4 flex items-center justify-center">{{ $wishlistCount }}</span>
                        @endif
                    </a>
               
              
                <a href="{{ route('cart.show') }}" class="relative hover:text-brand" title="Cart">
                    🛒 Cart
                    @if($cartCount > 0)
                        <span class="absolute -top-2 -right-3 bg-brand-600 text-black text-[10px] rounded-full w-4 h-4 flex items-center justify-center">{{ $cartCount }}</span>
                    @endif
                </a>

                
                    <a href="{{ route('account.dashboard') }}" class="hover:text-brand">Hi, {{ Str::before(auth()->user()->name, ' ') }}</a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="hover:text-brand">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="hover:text-brand">Login</a>
                    <a href="{{ route('register') }}" class="bg-brand-600 text-black px-3 py-1.5 rounded-lg hover:bg-brand-700">Register</a>
                @endauth
            </nav>
        </div>
    </div>
</header>
