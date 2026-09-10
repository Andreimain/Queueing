<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')
            ->with([
                'hd' => 'lorma.edu',
            ])
            ->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->user();

        $email = strtolower($googleUser->getEmail());

        if (!str_ends_with($email, '@lorma.edu')) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Only LORMA accounts are allowed to sign in.',
                ]);
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Your LORMA account is not registered in the system.',
                ]);
        }

        Auth::login($user);

        request()->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }
}
