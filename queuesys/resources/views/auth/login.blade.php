<x-guest-layout>
    <x-slot name="title">
        Login
    </x-slot>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="w-full max-w-md mx-auto">

        <!-- Login Card -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-xl overflow-hidden">

            <!-- Header -->
            <div class="px-8 pt-9 pb-7 text-center border-b border-gray-100">
                <div class="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M15 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM4 20a8 8 0 0 1 16 0H4Z" />
                    </svg>
                </div>

                <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                    Welcome back!
                </h1>

                <p class="mt-2 text-sm text-gray-500">
                    Sign in to access your queue management dashboard.
                </p>
            </div>

            <!-- Form -->
            <div class="px-8 py-8">
                <form method="POST" action="{{ route('login') }}" autocomplete="on" class="space-y-5">
                    @csrf

                    <!-- Email -->
                    <div>
                        <x-input-label
                            for="email"
                            :value="__('Email Address')"
                            class="mb-2 text-sm font-semibold text-gray-700"
                        />

                        <x-text-input
                            id="email"
                            type="email"
                            name="email"
                            :value="old('email')"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="Enter your email address"
                            class="block w-full rounded-xl border-gray-300 bg-gray-50 px-4 py-3 text-sm shadow-sm transition placeholder:text-gray-400 focus:border-emerald-500 focus:bg-white focus:ring-emerald-500"
                        />

                        <x-input-error
                            :messages="$errors->get('email')"
                            class="mt-2 text-sm text-red-600"
                        />
                    </div>

                    <!-- Password -->
                    <div>
                        <x-input-label
                            for="password"
                            :value="__('Password')"
                            class="mb-2 text-sm font-semibold text-gray-700"
                        />

                        <x-text-input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="Enter your password"
                            class="block w-full rounded-xl border-gray-300 bg-gray-50 px-4 py-3 text-sm shadow-sm transition placeholder:text-gray-400 focus:border-emerald-500 focus:bg-white focus:ring-emerald-500"
                        />

                        <x-input-error
                            :messages="$errors->get('password')"
                            class="mt-2 text-sm text-red-600"
                        />
                    </div>

                    <!-- Remember Me / Forgot Password -->
                    <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                        <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer">
                            <input
                                id="remember_me"
                                type="checkbox"
                                name="remember"
                                class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500"
                            >

                            <span class="text-sm text-gray-600">
                                {{ __('Remember me') }}
                            </span>
                        </label>

                        @if (Route::has('password.request'))
                            <a
                                href="{{ route('password.request') }}"
                                class="text-sm font-semibold text-emerald-600 transition hover:text-emerald-700"
                            >
                                {{ __('Forgot password?') }}
                            </a>
                        @endif
                    </div>

                    <!-- Login Button -->
                    <div class="pt-2">
                        <x-primary-button
                            class="w-full justify-center rounded-xl bg-emerald-600 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                        >
                            {{ __('Sign in') }}
                        </x-primary-button>
                    </div>
                </form>

                <!-- Divider -->
                <div class="relative my-7">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>

                    <div class="relative flex justify-center">
                        <span class="bg-white px-4 text-xs font-medium uppercase tracking-wider text-gray-400">
                            Or continue with
                        </span>
                    </div>
                </div>

                <!-- Google Login -->
                <a
                    href="{{ route('google.redirect') }}"
                    class="inline-flex w-full items-center justify-center gap-3 rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm font-semibold text-gray-700 shadow-sm transition hover:border-gray-400 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill="#4285F4" d="M21.35 12.27c0-.79-.07-1.55-.2-2.27H12v4.3h5.23a4.47 4.47 0 0 1-1.94 2.93v2.44h3.14c1.84-1.69 2.92-4.18 2.92-7.4z"/>
                        <path fill="#34A853" d="M12 21.8c2.63 0 4.84-.87 6.45-2.36l-3.14-2.44c-.87.58-1.98.93-3.31.93-2.54 0-4.69-1.72-5.46-4.03H3.29v2.52A9.75 9.75 0 0 0 12 21.8z"/>
                        <path fill="#FBBC05" d="M6.54 13.9a5.86 5.86 0 0 1 0-3.8V7.58H3.29a9.75 9.75 0 0 0 0 8.84l3.25-2.52z"/>
                        <path fill="#EA4335" d="M12 6.07c1.43 0 2.72.49 3.73 1.45l2.8-2.8C16.83 3.12 14.63 2.2 12 2.2a9.75 9.75 0 0 0-8.71 5.38l3.25 2.52C7.31 7.79 9.46 6.07 12 6.07z"/>
                    </svg>

                    Continue with Google
                </a>

                <!-- Register -->
                @if (Route::has('register'))
                    <p class="mt-7 text-center text-sm text-gray-500">
                        Don't have an account?

                        <a
                            href="{{ route('register') }}"
                            class="font-semibold text-emerald-600 transition hover:text-emerald-700"
                        >
                            Create an account
                        </a>
                    </p>
                @endif
            </div>
        </div>

        <!-- Footer -->
        <p class="mt-6 text-center text-xs text-gray-400">
            Secure access to the Queue Management System
        </p>
    </div>
</x-guest-layout>
