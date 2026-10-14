<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {
    }

    public function show(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = strtolower($credentials['email']) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()->withErrors([
                'email' => "Too many attempts. Try again in {$seconds} seconds."
            ]);
        }

        if (! Auth::attempt(
            $credentials,
            $request->boolean('remember')
        )) {
            RateLimiter::hit($throttleKey, 60);

            return back()
                ->withErrors([
                    'email' => 'These credentials do not match our records.'
                ])
                ->onlyInput('email');
        }

        RateLimiter::clear($throttleKey);

        /*
        |--------------------------------------------------------------------------
        | Regenerate session after login
        |--------------------------------------------------------------------------
        */

        $request->session()->regenerate();

        /*
        |--------------------------------------------------------------------------
        | Merge guest cart into logged-in user's cart
        |--------------------------------------------------------------------------
        */

        $this->cartService->mergeGuestCartIntoUser(
            $request,
            $request->user()
        );

        /*
        |--------------------------------------------------------------------------
        | Admin / Customer redirect
        |--------------------------------------------------------------------------
        */

        if ($request->user()->isAdmin()) {
            return redirect()->intended(
                route('admin.dashboard')
            );
        }

        return redirect()->intended(
            route('home')
        );
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}