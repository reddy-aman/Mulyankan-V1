<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- form heding  -->
        <div class=" flex justify-center items-center border w-50 rounded-lg py-1 mt-3 border-gray-300">
            <h1 class="font-medium text-blue-800">Register Your self </h1>
        </div>

        <!-- Name -->
        <div class="mt-4 mb-4">
        <div class="relative z-0 w-full mb-5 group">
            <input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name"  class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " required />
            <label for="name" :value="__('Name')" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Enter Your Name <span class="text-red-600">*</span>  </label>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>
        </div>
                    
        <!-- Email Address -->
        <div class="mt-4 mb-4">
        <div class="relative z-0 w-full mb-5 group">
            <input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " required />
            <label for="email" :value="__('Email')" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Enter Your Email <span class="text-red-600">*</span> </label>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        </div>

         <!-- Role Selection -->

         <div class="mt-4 mb-4">
        <label for="role" class="block mb-4 mt-2 text-sm font-semibold text-gray-900 dark:text-white">Choose Role <span class="text-red-600">*</span> </label>
        <select id="role" name="role" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
        <option value="" disabled selected>{{ __('Select your role') }}</option>
        <option value="Student" {{ old('role') == 'Student' ? 'selected' : '' }}>Student</option>
                <option value="Instructor" {{ old('role') == 'Instructor' ? 'selected' : '' }}>Instructor</option>
                <option value="TA" {{ old('role') == 'TA' ? 'selected' : '' }}>TA</option>
        </select>
        @error('role')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>


        <!-- Password -->
        <div class="mt-4 mb-4">
        <div class="relative z-0 w-full mb-5 group">
            <input id="password" type="password" name="password" required autocomplete="new-password" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " required />
            <label for="password" :value="__('Password')" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Enter New Password <span class="text-red-600">*</span>  </label>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        </div>

        <!-- Confirm Password -->
        <div class="mt-4 mb-4">
        <div class="relative z-0 w-full mb-5 group">
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " required />
            <label for="password_confirmation" :value="__('Confirm Password')"  class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Confirm password <span class="text-red-600">*</span> </label>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>
        </div>

    <!-- Already account Selection -->

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
