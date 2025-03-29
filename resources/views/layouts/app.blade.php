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
    <!-- used for the icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        window.addUserUrl = @json(route('courses.addUser'));
        window.uploadCSVUrl = @json(route('courses.uploadCSV'));
        window.deleteUserUrl = @json(route('courses.deleteUser', ['email' => '__EMAIL__']));
        window.editUserUrl = @json(route('courses.editUser', ['email' => '__EMAIL__']));
        @if (session('last_opened_course'))
            window.rosterShowUrl = @json(route('courses.roster', ['id' => session('last_opened_course')]));
        @else
            window.rosterShowUrl = "";
        @endif
    </script>
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
</body>

</html>
