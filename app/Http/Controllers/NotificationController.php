<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = $request->user()->notifications()->latest()->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function read(Request $request, string $notification): RedirectResponse
    {
        $record = $request->user()->notifications()->whereKey($notification)->firstOrFail();
        $record->markAsRead();

        $url = $record->data['url'] ?? null;
        if (is_string($url)
            && parse_url($url, PHP_URL_HOST) === parse_url(url('/'), PHP_URL_HOST)
            && parse_url($url, PHP_URL_SCHEME) === parse_url(url('/'), PHP_URL_SCHEME)) {
            return redirect()->to($url);
        }

        return back();
    }
}
