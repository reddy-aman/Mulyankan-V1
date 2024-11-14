<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Mulyankan</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="/mulyankan/font-awesome-4.7.0/css/font-awesome.min.css">


        <!-- Scripts -->
        @vite(['resources/css/app.css','resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')
            @include('layouts.sidebar')
            
            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <div class="p-4 sm:ml-64 ">
        <h1 class="text-center text-gray-600 text-1xl font-semibold"> ©2024 Website Designed, Developed and Hosted by CSE | IIT-B </h1>
        </div>
    </body>
</html>
