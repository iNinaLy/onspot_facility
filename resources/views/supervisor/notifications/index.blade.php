@extends('layouts.app')

@push('styles')
<style>
    /* General Styling */
    .page-header {
        margin-top: 5rem;
    }

    /* Modern Scrollbar */
    .notifications-list::-webkit-scrollbar {
        width: 8px;
    }

    .notifications-list::-webkit-scrollbar-track {
        background: #f9f9f9;
    }

    .notifications-list::-webkit-scrollbar-thumb {
        background-color: #d1d1d1;
        border-radius: 4px;
        border: 2px solid #f9f9f9;
    }

    .notifications-list {
        scrollbar-width: thin;
        scrollbar-color: #d1d1d1 #f9f9f9;
    }

    /* Notification Card Styling */
    .notification-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.5rem;
        background-color: #fff;
        border-radius: 0.75rem;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s, box-shadow 0.3s;
    }

    .notification-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .notification-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 4rem;
        height: 4rem;
        background-color: #e5f4fd;
        color: #2196f3;
        border-radius: 50%;
        font-size: 1.5rem;
    }

    .notification-content {
        flex: 1;
    }

    .notification-title {
        font-weight: 600;
        color: #2d3748;
        font-size: 1.25rem;
        margin-bottom: 0.25rem;
    }

    .notification-message {
        color: #4a5568;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }

    .notification-time {
        color: #718096;
        font-size: 0.75rem;
    }

    .unread-badge {
        background-color: #fed7d7;
        color: #c53030;
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 9999px;
        font-weight: 600;
    }

    /* Success Alert */
    .alert {
        padding: 1rem;
        border: 1px solid;
        border-radius: 0.5rem;
        font-weight: 600;
        margin-top: 1rem;
        margin-bottom: 1.5rem;
    }

    .alert-success {
        background-color: #e6fffa;
        border-color: #81e6d9;
        color: #285e61;
    }
</style>
@endpush

@section('content')
<div class="pt-16">
    <div class="container mx-auto px-4 py-8">
        <!-- Page Header -->
        <div class="flex justify-between items-center page-header">
            <h1 class="text-4xl font-bold text-gray-900">Notifications</h1>
            <div class="flex items-center space-x-4">
                <!-- Mark All as Read -->
                <form action="{{ route('supervisor.notifications.markAllAsRead') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Mark All as Read
                    </button>
                </form>

                <!-- Clear All Notifications -->
                <button onclick="openConfirmationModal()" class="text-red-600 hover:text-red-800 text-sm font-medium">
                    Clear All
                </button>
            </div>
        </div>

        <!-- Notifications List -->
        @if($notifications->count())
            <div class="notifications-list space-y-6 mt-8">
                @foreach($notifications as $notification)
                    <div class="notification-card">
                        <!-- Notification Icon -->
                        <div class="notification-icon">
                            @php
                                $iconClass = 'fa-info-circle'; // Default icon
                                if(isset($notification->data['type'])) {
                                    switch($notification->data['type']) {
                                        case 'alert':
                                            $iconClass = 'fa-exclamation-triangle';
                                            break;
                                        case 'confirmation':
                                            $iconClass = 'fa-check-circle';
                                            break;
                                        case 'message':
                                            $iconClass = 'fa-envelope';
                                            break;
                                    }
                                }
                            @endphp
                            <i class="fa {{ $iconClass }}"></i>
                        </div>

                        <!-- Notification Content -->
                        <div class="notification-content">
                            <h3 class="notification-title">{{ $notification->data['title'] ?? 'Notification' }}</h3>
                            <p class="notification-message">{{ $notification->data['message'] ?? '' }}</p>
                            <div class="flex justify-between items-center">
                                <span class="notification-time">{{ $notification->created_at->diffForHumans() }}</span>
                                @if(!$notification->read_at)
                                    <span class="unread-badge">Unread</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-10">
                {{ $notifications->links('pagination::tailwind') }}
            </div>
        @else
            <!-- No Notifications Message -->
            <div class="bg-white shadow-lg rounded-lg p-10 text-center mt-8">
                
                <p class="text-gray-700 text-lg">No notifications found.</p>
            </div>

        @endif

        <!-- Success Alert -->
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmationModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-md shadow-lg">
        <h3 class="text-lg font-semibold text-gray-800">Confirm Clear All</h3>
        <p class="text-sm text-gray-600 mt-2">Are you sure you want to clear all notifications? This action cannot be undone.</p>
        <div class="mt-6 flex justify-end space-x-4">
            <button onclick="closeConfirmationModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-md">
                Cancel
            </button>
            <form action="{{ route('supervisor.notifications.clearAll') }}" method="POST">
                @csrf
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md">
                    Clear All
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function openConfirmationModal() {
        document.getElementById('confirmationModal').classList.remove('hidden');
    }

    function closeConfirmationModal() {
        document.getElementById('confirmationModal').classList.add('hidden');
    }
</script>
@endsection
