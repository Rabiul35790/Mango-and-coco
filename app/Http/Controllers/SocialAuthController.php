<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\VideoOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /** Send the buyer to Google (works for both login and registration). */
    public function redirect()
    {
        if (! config('services.google.client_id') || ! config('services.google.client_secret')) {
            return redirect('/login')->withErrors([
                'email' => 'Google login is not connected yet — use email login for now.',
            ]);
        }

        return Socialite::driver('google')->redirect();
    }

    /** Google sends the buyer back here — find or create, link history, log in. */
    public function callback(Request $request)
    {
        try {
            $google = Socialite::driver('google')->user();
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            // Session did not survive the Google round-trip. Almost always
            // means the browser host changed mid-flow (localhost vs
            // 127.0.0.1 have separate cookies) or the button was double-hit.
            report($e);

            return redirect('/login')->withErrors([
                'email' => 'Google sign-in session expired — open the site at http://127.0.0.1:8000 (not localhost) and try once more.',
            ]);
        } catch (\Throwable $e) {
            report($e);

            return redirect('/login')->withErrors([
                'email' => 'Google login failed — please try email login.',
            ]);
        }

        if (! filled($google->getEmail())) {
            return redirect('/login')->withErrors([
                'email' => 'Google did not share an email — please use email login.',
            ]);
        }

        $user = User::where('google_id', $google->getId())->first()
            ?? User::where('email', $google->getEmail())->first();

        if ($user) {
            $user->forceFill([
                'google_id' => $google->getId(),
                'name' => $user->name ?: $google->getName(),
                'email_verified_at' => $user->email_verified_at ?? now(),
            ])->save();
        } else {
            $user = User::create([
                'name' => $google->getName() ?: explode('@', $google->getEmail())[0],
                'email' => $google->getEmail(),
                'password' => Hash::make(Str::random(40)),
                'google_id' => $google->getId(),
                'email_verified_at' => now(),
            ]);
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        // Same guest-history linking as email login/register.
        Order::where('customer_email', $user->email)->whereNull('user_id')->update(['user_id' => $user->id]);
        VideoOrder::where('customer_email', $user->email)->whereNull('user_id')->update(['user_id' => $user->id]);

        return redirect()->intended('/account');
    }
}
