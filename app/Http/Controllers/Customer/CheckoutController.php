<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(protected CartService $cartService, protected OrderService $orderService)
    {
    }

    public function show(Request $request): View|RedirectResponse
    {
        $cart = $this->cartService->currentCart($request)->load('items.product', 'items.variant');

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.show')->with('status', 'Your cart is empty — add something before checking out.');
        }

        $addresses = Auth::user()->addresses()->orderByDesc('is_default')->get();

        return view('customer.checkout.show', compact('cart', 'addresses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'address_line' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:120'],
            'area' => ['nullable', 'string', 'max:120'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'shipping_method' => ['required', 'in:standard,express'],
            'payment_method' => ['required', 'in:cod,bkash,nagad,sslcommerz'],
            'order_notes' => ['nullable', 'string', 'max:1000'],
            'save_address' => ['nullable', 'boolean'],
        ]);

        if ($data['payment_method'] !== 'cod') {
            return back()->withErrors([
                'payment_method' => 'Online payment gateways are being finalized — please choose Cash on Delivery for now.',
            ])->withInput();
        }

        $cart = $this->cartService->currentCart($request);

        try {
            $order = $this->orderService->createFromCart($cart, $request->user(), $data);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        if ($request->boolean('save_address')) {
            $request->user()->addresses()->create([
                'full_name' => $data['full_name'],
                'phone' => $data['phone'],
                'address_line' => $data['address_line'],
                'city' => $data['city'],
                'area' => $data['area'] ?? null,
                'postal_code' => $data['postal_code'] ?? null,
                'is_default' => $request->user()->addresses()->count() === 0,
            ]);
        }

        return redirect()->route('checkout.confirmation', $order)->with('status', 'Your order has been placed!');
    }

    public function confirmation(Request $request, \App\Models\Order $order): View
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $order->load('items');

        return view('customer.checkout.confirmation', compact('order'));
    }
}
