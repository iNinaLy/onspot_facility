@extends('layouts.app') <!-- Use your existing layout -->

@section('content')
<div class="container mx-auto">
    <h1 class="text-2xl font-bold mb-4">Notifications</h1>

    <!-- Unread Notifications -->
    <div class="mb-6">
        <h2 class="text-xl font-semibold mb-2">Unread Notifications</h2>
        @if($unreadNotifications->isEmpty())
            <p class="text-gray-500">No unread notifications.</p>
        @else
            <ul class="list-disc list-inside">
                @foreach($unreadNotifications as $notification)
                    <li class="mb-2">
                        <a href="{{ route('notifications.mark-as-read', $notification->id) }}"
                           class="text-blue-500 hover:underline">
                            {{ $notification->data['message'] ?? 'Notification' }}
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <!-- All Notifications -->
    <div>
        <h2 class="text-xl font-semibold mb-2">All Notifications</h2>
        @if($notifications->isEmpty())
            <p class="text-gray-500">No notifications.</p>
        @else
            <ul class="list-disc list-inside">
                @foreach($notifications as $notification)
                    <li class="mb-2">
                        <span class="{{ $notification->read_at ? 'text-gray-500' : 'text-black font-bold' }}">
                            {{ $notification->data['message'] ?? 'Notification' }}
                        </span>
                        <span class="text-gray-400 text-sm ml-2">
                            {{ $notification->created_at->diffForHumans() }}
                        </span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
