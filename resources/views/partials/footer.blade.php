<footer class="bg-gray-900 text-gray-300 mt-16">
    <div class="max-w-7xl mx-auto px-4 py-10 grid grid-cols-1 md:grid-cols-4 gap-8 text-sm">
        <div>
            <h3 class="text-white text-lg font-bold mb-3">R-SELFASSION</h3>
            <p>Your modern destination for quality products at honest prices.</p>
        </div>
        <div>
            <h4 class="text-white font-semibold mb-3">Shop</h4>
            <ul class="space-y-2">
                <li><a href="{{ route('products.index') }}" class="hover:text-white">All Products</a></li>
                <li><a href="{{ route('products.index', ['sort' => 'popularity']) }}" class="hover:text-white">Best Sellers</a></li>
            </ul>
        </div>
        <div>
            <h4 class="text-white font-semibold mb-3">Account</h4>
            <ul class="space-y-2">
                <li><a href="{{ route('login') }}" class="hover:text-white">Login</a></li>
                <li><a href="{{ route('register') }}" class="hover:text-white">Register</a></li>
            </ul>
        </div>
        <div>
            <h4 class="text-white font-semibold mb-3">Newsletter</h4>
            <form class="flex">
                <input type="email" placeholder="Your email" class="w-full rounded-l-lg px-3 py-2 text-gray-900 text-sm focus:outline-none">
                <button type="button" class="bg-brand px-3 rounded-r-lg text-white text-sm">Join</button>
            </form>
        </div>
    </div>
    <div class="border-t border-gray-800 text-center text-xs py-4">
        &copy; {{ date('Y') }} R-SELFASSION. All rights reserved.
    </div>
</footer>
