<x-guest-layout>
    <div class="absolute inset-0 bg-black bg-opacity-30 backdrop-blur-sm z-40"></div>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4 relative z-50" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="relative z-50">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" class="text-white" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" class="text-white" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me"  class="inline-flex items-center ">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-500 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-white">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-white hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3 text-white">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
