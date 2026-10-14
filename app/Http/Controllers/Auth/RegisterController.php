<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wishlist;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {
    }

    public function show(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255'
            ],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email'
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30'
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed'
            ],
        ])->validate();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
            'is_active' => true,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Create Wishlist
        |--------------------------------------------------------------------------
        */

        Wishlist::firstOrCreate([
            'user_id' => $user->id,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Login newly registered user
        |--------------------------------------------------------------------------
        */

        Auth::login($user);

        $request->session()->regenerate();

        /*
        |--------------------------------------------------------------------------
        | Merge guest cart
        |--------------------------------------------------------------------------
        */

        $this->cartService->mergeGuestCartIntoUser(
            $request,
            $user
        );

        return redirect()
            ->route('home')
            ->with(
                'status',
                'Welcome to ShopNova, ' . $user->name . '!'
            );
    }
}