<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ShopNova - Shop Smarter')</title>
    <meta name="description" content="@yield('meta_description', 'ShopNova - your modern online shopping destination.')">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('head')
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
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
    @stack('scripts')
</body>
</html>
