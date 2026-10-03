<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Inertia\Inertia;
use Inertia\Response;

class AuthController extends Controller
{
    public function showLogin(): Response
    {
        return Inertia::render('Auth/Login', ['meta' => ['title' => 'Log in — Mango&Coco']]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email|max:200',
            'password' => 'required|string',
            'remember' => 'nullable|boolean',
        ]);

        if (! Auth::attempt(
            ['email' => $credentials['email'], 'password' => $credentials['password']],
            (bool) ($credentials['remember'] ?? false)
        )) {
            return back()->withErrors(['email' => 'These credentials do not match our records.']);
        }

        $request->session()->regenerate();

        // Link any past guest orders/briefs to this account by email.
        $user = $request->user();
        \App\Models\Order::where('customer_email', $user->email)->whereNull('user_id')->update(['user_id' => $user->id]);
        \App\Models\VideoOrder::where('customer_email', $user->email)->whereNull('user_id')->update(['user_id' => $user->id]);

        return redirect()->intended('/account');
    }

    public function showRegister(): Response
    {
        return Inertia::render('Auth/Register', ['meta' => ['title' => 'Create account — Mango&Coco']]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:200|unique:users,email',
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        \App\Models\Order::where('customer_email', $user->email)->whereNull('user_id')->update(['user_id' => $user->id]);
        \App\Models\VideoOrder::where('customer_email', $user->email)->whereNull('user_id')->update(['user_id' => $user->id]);

        return redirect('/account');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function showForgot(): Response
    {
        return Inertia::render('Auth/ForgotPassword', ['meta' => ['title' => 'Reset password — Mango&Coco']]);
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email|max:200']);
        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'Reset link sent — check your email.')
            : back()->withErrors(['email' => __($status)]);
    }

    public function showReset(string $token): Response
    {
        return Inertia::render('Auth/ResetPassword', [
            'meta' => ['title' => 'Set new password — Mango&Coco'],
            'token' => $token,
            'email' => request('email', ''),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|max:200',
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect('/login')->with('success', 'Password updated — please log in.')
            : back()->withErrors(['email' => __($status)]);
    }
}
