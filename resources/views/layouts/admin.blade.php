<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin - ShopNova')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { brand: { DEFAULT: '#ff5722', dark: '#e64a19' } } } } }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.4/chart.umd.min.js"></script>
</head>
<body class="bg-gray-100 text-gray-800">
<div class="flex min-h-screen">

    <aside class="w-64 bg-gray-900 text-gray-300 flex flex-col shrink-0">
        <div class="px-6 py-5 text-xl font-extrabold text-white border-b border-gray-800">
            R-SEL<span class="text-brand">FASSION</span> <span class="text-xs font-normal text-gray-400 block">Admin Panel</span>
        </div>
        <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
            @php
                $links = [
                    ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => '📊'],
                    ['route' => 'admin.products.index', 'label' => 'Products', 'icon' => '📦'],
                    ['route' => 'admin.categories.index', 'label' => 'Categories', 'icon' => '🗂️'],
                    ['route' => 'admin.brands.index', 'label' => 'Brands', 'icon' => '🏷️'],
                    ['route' => 'admin.orders.index', 'label' => 'Orders', 'icon' => '🧾'],
                    ['route' => 'admin.customers.index', 'label' => 'Customers', 'icon' => '👥'],
                    ['route' => 'admin.coupons.index', 'label' => 'Coupons', 'icon' => '🎟️'],
                    ['route' => 'admin.reviews.index', 'label' => 'Reviews', 'icon' => '⭐'],
                    ['route' => 'admin.reports.index', 'label' => 'Reports', 'icon' => '📈'],
                ];
            @endphp
            @foreach($links as $link)
                <a href="{{ route($link['route']) }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-800 hover:text-white {{ request()->routeIs(str_replace('.index', '.*', $link['route'])) || request()->routeIs($link['route']) ? 'bg-gray-800 text-white' : '' }}">
                    <span>{{ $link['icon'] }}</span> {{ $link['label'] }}
                </a>
            @endforeach
        </nav>
        <div class="px-4 py-4 border-t border-gray-800 text-xs">
            <a href="{{ route('home') }}" class="hover:text-white block mb-2">← Back to storefront</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="hover:text-white">Logout ({{ auth()->user()->name }})</button>
            </form>
        </div>
    </aside>

    <main class="flex-1 min-w-0">
        <div class="bg-white border-b px-8 py-4 flex items-center justify-between">
            <h1 class="text-xl font-bold">@yield('page_title', 'Dashboard')</h1>
        </div>

        <div class="p-8">
            @if (session('status'))
                <div class="bg-green-50 text-green-700 text-sm rounded-lg p-3 mb-6">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-50 text-red-700 text-sm rounded-lg p-3 mb-6">
                    @foreach ($errors->all() as $error) <p>{{ $error }}</p> @endforeach
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</div>
</body>
</html>
