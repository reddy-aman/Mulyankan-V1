<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- error message -->
    @if ($errors->has('role'))
    <div id="toast-error" class="flex items-center w-full max-w-100 p-4 mb-4 text-gray-500 bg-white rounded-lg shadow dark:text-gray-400 dark:bg-gray-800" role="alert">
        <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-red-500 bg-red-100 rounded-lg dark:bg-red-800 dark:text-red-200">
        <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
            <span class="sr-only">Error icon</span>
        </div>
        <span class="ml-3 font-medium text-red-700">{{ $errors->first('role') }}</span>
    </div>

    <script>
        // Set timeout for auto-dismiss in seconds
        setTimeout(function() {
            document.getElementById('toast-error').style.display = 'none';
        }, 8000); // 5000 milliseconds = 5 seconds
    </script>
@endif


    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Enter Your Email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password"
                            placeholder="Enter Your Password" />
                            
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Role Selection -->
        <div class="mt-4">
            <x-input-label for="role" :value="__('Role')" />
            <select id="role" name="role" class="block mt-1 w-full" required>
                <option value="" disabled selected>{{ __('Select your role') }}</option>
                <option value="Student" {{ old('role') == 'Student' ? 'selected' : '' }}>Student</option>
                <option value="Instructor" {{ old('role') == 'Instructor' ? 'selected' : '' }}>Instructor</option>
                <option value="TA" {{ old('role') == 'TA' ? 'selected' : '' }}>TA</option>
            </select>
            <!-- <x-input-error :messages="$errors->get('role')" class="mt-2" /> -->
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
