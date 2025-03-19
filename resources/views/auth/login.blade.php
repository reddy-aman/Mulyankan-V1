<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf
        
        <!-- Email Address -->
        <div class="mb-5">
            <label for="email" :value="__('Email')" class="block mb-2 mt-2 text-sm font-semibold text-gray-900 dark:text-white">Username <span class="text-red-600">*</span> </label>
            <div class="flex">
            <span class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-e-0 border-gray-300 rounded-s-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm0 5a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm0 13a8.949 8.949 0 0 1-4.951-1.488A3.987 3.987 0 0 1 9 13h2a3.987 3.987 0 0 1 3.951 3.512A8.949 8.949 0 0 1 10 18Z"/>
            </svg>
            </span>
            <input id="email" type="email" id="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Enter Your Email" required class="rounded-none rounded-e-lg bg-gray-50 border border-gray-300 text-gray-900 focus:ring-blue-500 focus:border-blue-500 block flex-1 min-w-0 w-full text-sm p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
        </div>
        </div>
        <!-- error  -->
        <div>
            <x-input-error :messages="$errors->get('email')" class="mt-4" />
        </div>

        <!-- Password -->

        <div class="mb-5 ">
        <label for="password" :value="__('Password')"  class="block mb-2 mt-2 text-sm font-semibold text-gray-900 dark:text-white">Password <span class="text-red-600">*</span> </label>
            <div class="flex">
            <span class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-e-0 border-gray-300 rounded-s-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
            <i class="fa fa-key" aria-hidden="true"></i>
            </span>
            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Enter Your Password" placeholder="Enter Your Email" required class="rounded-none rounded-e-lg bg-gray-50 border border-gray-300 text-gray-900 focus:ring-blue-500 focus:border-blue-500 block flex-1 min-w-0 w-full text-sm p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
        </div>
        </div>

         <!-- error  -->
         <div>
         <x-input-error :messages="$errors->get('password')" class="mt-2" />
         </div>
        
        <!-- Role Selection -->
        <div class="mb-5">
        <label for="role" :value="__('Role')" class="block mb-2 mt-2 text-sm font-semibold text-gray-900 dark:text-white">Choose Role <span class="text-red-600">*</span> </label>
        <select id="role" name="role" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
        <option value="" disabled selected>{{ __('Select your role') }}</option>
                <option value="Student" {{ old('role') == 'Student' ? 'selected' : '' }}>Student</option>
                <option value="Instructor" {{ old('role') == 'Instructor' ? 'selected' : '' }}>Instructor</option>
                <option value="TA" {{ old('role') == 'TA' ? 'selected' : '' }}>TA</option>
        </select>
        </div>
        <!-- error -->
        <div class="mt-4">
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="mb-5">
        <div class="flex items-center mb-4">
        <input id="remember_me" type="checkbox" value="" name="remember" class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 rounded dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" >
        <label for="remember_me" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300"> Do you want to remember your password ? </label>
        </div>
        </div>
    

        <!-- forget password -->
        <div class="mb-5">
        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-blue-800 font-semibold hover:text-blue-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
            <i class="fa fa-sign-in mr-2" aria-hidden="true"></i> {{ __('Log in') }}
            </x-primary-button>
        </div>
        </div>
        
    </form>

</x-guest-layout>
