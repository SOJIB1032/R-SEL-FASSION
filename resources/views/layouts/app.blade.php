<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'R-SELFASSION - Shop Smarter')</title>
    <meta name="description" content="@yield('meta_description', 'R-SELFASSION - your modern online shopping destination.')">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { brand: { DEFAULT: '#ff5722', dark: '#e64a19' } } } } }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">

    @include('partials.navbar')

    @if (session('status'))
        <div class="max-w-7xl mx-auto w-full px-4 mt-4">
            <div class="bg-green-100 text-green-800 border border-green-300 rounded-lg px-4 py-3 text-sm">
                {{ session('status') }}
            </div>
        </div>
    @endif

    <main class="flex-1">
        @yield('content')
    </main>

    @include('partials.footer')
</body>
</html>
