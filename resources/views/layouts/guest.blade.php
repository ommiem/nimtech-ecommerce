<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://analytics.ahrefs.com/analytics.js" data-key="u2ZxmcB5OL+usxwN/WCNDA" async></script>
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gray-50">
        <header class="bg-white border-b">
            @php($settings = \App\Models\Setting::getCached())
            <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
                <a href="{{ route('products.index') }}" class="text-xl font-semibold tracking-tight">{{ $settings->site_name ?? 'Shoply' }}</a>
                <a href="{{ route('products.index') }}" class="text-sm hover:underline">Back to Store</a>
            </div>
        </header>
        <div class="min-h-screen flex flex-col items-center pt-10">
            <div class="w-full sm:max-w-md px-6 py-6 bg-white border rounded shadow-sm">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
