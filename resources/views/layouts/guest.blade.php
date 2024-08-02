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
    </head>
    <body class="font-sans bg-[url('/images/bgImage.jpg')] text-gray-900 antialiased">
        <div class="min-h-screen bg-[url('/public/images/bgImage.jpg')] bg-center bg-cover  flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div class="absolute inset-0 bg-black bg-opacity-50 backdrop-blur-sm"></div>
            <div>

            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 z-1   bg-center bg-cover  shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
