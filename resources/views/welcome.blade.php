<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Mulyankan</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../font-awesome-4.7.0/css/font-awesome.min.css">

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
       
    @endif
</head>

<body class="font-sans antialiased dark:bg-black">

    <section
        class="bg-center bg-no-repeat bg-blend-multiply" style="background-color: #f2f5f6;">
        <div class="px-4 mx-auto max-w-screen-xl text-center py-24 lg:py-36">
                <x-application-logo/>
            <h1 class=" mt-2 mb-4 text-2xl font-semibold tracking-tight leading-none md:text-5xl lg:text-6xl" style="color: #003c46;">
            Grade Your Assessments Anywhere </h1>
            <p class="mb-8 text-lg font-norma lg:text-xl sm:px-16 lg:px-48" style="color: #7f8080;">
            Mulyankan helps you seamlessly administer and grade all of your assessments, whether online or in-class. Save time grading and get a clear picture of how your students are doing
        </p>
        @if (Route::has('login'))
            <div class="flex flex-col space-y-4 sm:flex-row sm:justify-center sm:space-y-0">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white">
                            Dashboard
                        </a>
                    @else
                    <a href="{{ route('login') }}" 
                        class="relative h-11 inline-flex items-center justify-center p-0.5  text-black me-2 overflow-hidden text-sm font-medium rounded-lg">
                            <span class="relative shadow-lg px-6  py-2.5 transition-all ease-in duration-75 rounded-md group-hover:bg-opacity-0" style="background-color: #00ffaa;">
                                Login <i class="fa fa-sign-in ml-1" aria-hidden="true"></i>
                            </span>
                        </a>
                        @if (Route::has('register'))
                        
                            <a href="{{ route('register') }}" 
                            class="relative h-11 inline-flex items-center justify-center p-0.5 me-2 overflow-hidden text-sm font-medium rounded-lg" style="color: #00ffaa;">
                                <span class="relative shadow-lg px-6  py-2.5 transition-all ease-in duration-75 rounded-md group-hover:bg-opacity-0" style="background-color: #003c46;">
                                    Register <i class="fa fa-long-arrow-right ml-1" aria-hidden="true"></i>
                                </span>
                            </a>
                            
                        @endif
                    @endauth
            </div>
            @endif
        </div>
    </section>

            <div style="padding: 30px; margin-top:30px;">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="max-h-30 max-w-30 rounded-lg">
                <video autoplay muted loop playsinline class="w-full h-auto rounded-lg shadow-lg">
                    <source src="./images/flexible_grading.mp4" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
                </div>
                <div class=" max-h-30 max-w-30 shadow-lg rounded-lg bg-slate-100">
                    <div class="p-5 mt-3">
                        <h3 class="mb-4 text-3xl font-extrabold text-gray-900 dark:text-white md:text-5xl lg:text-6xl">
                            <span class="text-transparent bg-clip-text bg-gradient-to-r to-emerald-600 from-sky-400">
                                Quick, Flexible Grading
                            </span>
                        </h3>
                        <p class="my-4 text-sm text-gray-500 sm:text-base md:text-lg">
                            Apply detailed feedback with just one click. Make rubric changes that apply to previously graded work.
                        </p>
                    </div>
                </div>
            </div>

            </div>
   
     <!-- footer section  -->
   
     <footer class="bg-gray-200 max-w-screen-xl rounded-lg shadow dark:bg-gray-900 m-2 mt-10 mb-3">
    <div class="w-full max-w-screen-xl mx-auto p-4 md:py-4">
        <span class="block text-sm text-gray-500 sm:text-center dark:text-gray-400">©2024 Website Designed, Developed and Hosted by CSE | IIT-B</span>
    </div>
</footer>

</body>

</html>