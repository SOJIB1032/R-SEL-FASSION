@extends('layouts.app')
@section('title', 'Notifications - ShopNova')
@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Notifications</h1>
    @if($notifications->isEmpty())
        <div class="bg-white rounded-xl p-10 text-center text-gray-500">You do not have any notifications yet.</div>
    @else
        <div class="bg-white rounded-xl shadow-sm divide-y">
            @foreach($notifications as $notification)
                <div class="p-4 flex items-start justify-between gap-4 {{ $notification->read_at ? '' : 'bg-orange-50' }}">
                    <div>
                        <p class="font-medium">{{ $notification->data['message'] ?? 'ShopNova update' }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex gap-3 items-center">
                        @if(!empty($notification->data['url']))
                            <a href="{{ $notification->data['url'] }}" class="text-brand text-sm hover:underline">Open</a>
                        @endif
                        @if(!$notification->read_at)
                            <form method="POST" action="{{ route('notifications.read', $notification->id) }}">@csrf<button class="text-xs text-gray-600 underline">Mark read</button></form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $notifications->links() }}</div>
    @endif
</div>
@endsection
