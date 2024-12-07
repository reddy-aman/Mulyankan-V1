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
        <link rel="stylesheet" href="./font-awesome-4.7.0/css/font-awesome.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css','resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 "
        style="background-image: linear-gradient(to top, #fddb92 0%, #d1fdff 100%);">
          

           <!-- error message -->
            @if ($errors->has('role'))
                <div id="toast-error" class="flex items-center w-50 p-4 mb-4 text-gray-500 bg-white rounded-lg shadow dark:text-gray-400 dark:bg-gray-800" role="alert">
                    <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-red-500 bg-red-100 rounded-lg dark:bg-red-800 dark:text-red-200">
                    <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
                        <span class="sr-only">Error icon</span>
                    </div>
                    <span class="ml-3 font-medium text-red-700">{{ $errors->first('role') }}</span>
                </div>
            @endif

            <!-- login form -->
            <div class=" bg-white w-full sm:max-w-md mt-6 px-6 py-6 shadow-lg overflow-hidden sm:rounded-2xl">
                 <!-- logo -->
                 <div class="mt-1 mb-1 flex justify-center items-center">
                    <a href="#">
                        <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                    </a>
                </div>

                <div class="mt-3 flex justify-center items-center">
                <h1 class="font-medium">Welcome to Mulyankan</h1>
                </div>
                
                {{ $slot }}
            </div>
        </div>
       <!-- footer -->
        <div class="p-4  ">
        <h1 class="text-center text-gray-600 text-1xl font-semibold"> ©2024 Website Designed, Developed and Hosted by CSE | IIT-B </h1>
        </div>
    </body>
<script>
    // Set timeout for auto-dismiss in seconds
    setTimeout(function() {
        document.getElementById('toast-error').style.display = 'none';
    }, 8000); // 5000 milliseconds = 5 seconds
</script>
</html>
