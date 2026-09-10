<x-guest-layout>
    <x-slot name="title">
        Login
    </x-slot>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="bg-white/80 backdrop-blur-lg shadow-2xl rounded-2xl p-10 w-full max-w-md border border-gray-200">

        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Welcome!</h1>
            <p class="text-gray-500 mt-2 text-sm">
                Sign in to continue to your dashboard
            </p>
        </div>

        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}" class="space-y-6" autocomplete="on">
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label
                    for="email"
                    :value="__('Email')"
                    class="block text-sm font-medium text-gray-700 mb-2"
                />

                <x-text-input
                    id="email"
                    class="block mt-1 w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 placeholder-gray-400 shadow-sm transition"
                    type="email"
                    name="email"
                    :value="old('email')"
                    required
                    autofocus
                    autocomplete="username"
                />

                <x-input-error
                    :messages="$errors->get('email')"
                    class="mt-2 text-red-500 text-sm"
                />
            </div>

            <!-- Password -->
            <div>
                <x-input-label
                    for="password"
                    :value="__('Password')"
                    class="block text-sm font-medium text-gray-700 mb-2"
                />

                <x-text-input
                    id="password"
                    class="block mt-1 w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 placeholder-gray-400 shadow-sm transition"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                />

                <x-input-error
                    :messages="$errors->get('password')"
                    class="mt-2 text-red-500 text-sm"
                />
            </div>

            <!-- Remember Me + Forgot Password -->
            <div class="flex items-center justify-between text-sm mt-4">

                <label for="remember_me" class="inline-flex items-center text-gray-600">
                    <input
                        id="remember_me"
                        type="checkbox"
                        class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500"
                        name="remember"
                    >

                    <span class="ms-2">
                        {{ __('Remember me') }}
                    </span>
                </label>

                @if (Route::has('password.request'))
                    <a
                        class="text-emerald-600 hover:text-emerald-700 font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
                        href="{{ route('password.request') }}"
                    >
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

            </div>

            <!-- Submit -->
            <div class="flex items-center justify-end mt-6">

                <x-primary-button
                    class="w-full justify-center bg-emerald-600 hover:bg-emerald-500 text-white font-semibold py-2.5 rounded-lg shadow-md transition duration-150"
                >
                    {{ __('Log in') }}
                </x-primary-button>

            </div>
        </form>

        <!-- Divider -->
        <div class="flex items-center my-6">
            <div class="flex-1 border-t border-gray-300"></div>

            <span class="px-4 text-sm text-gray-500">
                OR
            </span>

            <div class="flex-1 border-t border-gray-300"></div>
        </div>

        <!-- Google Login -->
        <a
            href="{{ route('google.redirect') }}"
            class="w-full inline-flex items-center justify-center gap-3 px-4 py-2.5 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition duration-150"
        >

            <!-- Google Logo -->
            <svg
                class="w-5 h-5"
                viewBox="0 0 24 24"
                aria-hidden="true"
            >
                <path
                    fill="#4285F4"
                    d="M21.35 12.27c0-.79-.07-1.55-.2-2.27H12v4.3h5.23a4.47 4.47 0 0 1-1.94 2.93v2.44h3.14c1.84-1.69 2.92-4.18 2.92-7.4z"
                />

                <path
                    fill="#34A853"
                    d="M12 21.8c2.63 0 4.84-.87 6.45-2.36l-3.14-2.44c-.87.58-1.98.93-3.31.93-2.54 0-4.69-1.72-5.46-4.03H3.29v2.52A9.75 9.75 0 0 0 12 21.8z"
                />

                <path
                    fill="#FBBC05"
                    d="M6.54 13.9a5.86 5.86 0 0 1 0-3.8V7.58H3.29a9.75 9.75 0 0 0 0 8.84l3.25-2.52z"
                />

                <path
                    fill="#EA4335"
                    d="M12 6.07c1.43 0 2.72.49 3.73 1.45l2.8-2.8C16.83 3.12 14.63 2.2 12 2.2a9.75 9.75 0 0 0-8.71 5.38l3.25 2.52C7.31 7.79 9.46 6.07 12 6.07z"
                />
            </svg>

            <span>
                Continue with Google
            </span>

        </a>

        <!-- Register Redirect -->
        @if (Route::has('register'))
            <p class="text-center text-gray-600 text-sm mt-6">
                Don’t have an account?

                <a
                    href="{{ route('register') }}"
                    class="text-emerald-600 hover:text-emerald-700 font-semibold"
                >
                    Create one
                </a>
            </p>
        @endif

    </div>
</x-guest-layout>
