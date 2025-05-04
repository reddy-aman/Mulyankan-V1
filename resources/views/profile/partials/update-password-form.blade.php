<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Current Password')" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_new_password" :value="__('New Password')" />
            <x-text-input id="update_password_new_password" name="new_password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->get('new_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_new_password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="update_password_new_password_confirmation" name="new_password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->get('new_password_confirmation')" class="mt-2" />
        </div>


        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status'))
                <div 
                    x-data="{ show: true }"
                    x-show="show"
                    x-init="setTimeout(() => show = false, 3000)"
                    x-transition
                    class="fixed top-5 right-5 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50"
                >
                    {{ session('status') }}
                </div>
            @endif

        </div>
    </form>
</section>
