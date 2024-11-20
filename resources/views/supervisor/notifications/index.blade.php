@extends('layouts.app')

@push('styles')
<style>
  
    .mb-6 {
        margin-bottom: 1.5rem;
        margin-top: 5rem;
    }

    /* Optional: Enhance scrollbar appearance for better aesthetics */

    /* For Webkit browsers */
    .notifications-list::-webkit-scrollbar {
        width: 8px;
    }

    .notifications-list::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .notifications-list::-webkit-scrollbar-thumb {
        background-color: #c1c1c1;
        border-radius: 4px;
        border: 2px solid #f1f1f1;
    }

    /* For Firefox */
    .notifications-list {
        scrollbar-width: thin;
        scrollbar-color: #c1c1c1 #f1f1f1;
    }
</style>
@endpush

@section('content')
<!-- Main Content Wrapper with Top Padding to Avoid Navbar Overlap -->
<div class="pt-16"> <!-- Adjust 'pt-16' based on your navbar's height -->
    <div class="container mx-auto px-4 py-8">
        <!-- Page Header with Custom Margin-Top -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="mt-4 text-3xl font-bold text-gray-800">Your Notifications</h1>
            <!-- Optional: Add a "Mark All as Read" button -->
            <form action="{{ route('supervisor.notifications.markAllAsRead') }}" method="POST">
                @csrf
                <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Mark All as Read
                </button>
            </form>
        </div>

        @if($notifications->count())
            <!-- Notifications List Container -->
            <div class="notifications-list space-y-4">
                @foreach($notifications as $notification)
                    <!-- Individual Notification Card -->
                    <div class="bg-white shadow-sm rounded-lg hover:shadow-md transition-shadow duration-300">
                        <a href="{{ isset($notification->data['complaint_id']) ? route('supervisor.complaints.show', $notification->data['complaint_id']) : '#' }}" class="flex items-center p-4">
                            <!-- Notification Icon -->
                            <div class="flex-shrink-0">
                                <div class="bg-blue-100 text-blue-600 rounded-full h-14 w-14 flex items-center justify-center">
                                    @php
                                        // Define icon classes based on notification type
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
                                                // Add more cases as needed
                                            }
                                        }
                                    @endphp
                                    <i class="fa {{ $iconClass }} text-xl"></i>
                                </div>
                            </div>

                            <!-- Notification Content -->
                            <div class="ml-4 flex-1">
                                <div class="flex justify-between items-center">
                                    <!-- Notification Title -->
                                    <h3 class="text-lg font-medium text-gray-800">
                                        {{ $notification->data['title'] ?? 'Notification' }}
                                    </h3>
                                    <!-- Notification Timestamp -->
                                    <span class="text-sm text-gray-500">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <!-- Notification Message -->
                                <p class="mt-2 text-gray-600">
                                    {{ $notification->data['message'] ?? '' }}
                                </p>

                                @if(!$notification->read_at)
                                    <!-- Unread Badge -->
                                    <span class="mt-3 inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                        Unread
                                    </span>
                                @endif
                            </div>

                            <!-- Chevron Icon for Navigation -->
                            <div class="ml-2 flex-shrink-0">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            <!-- Pagination Controls -->
            <div class="mt-8">
                {{ $notifications->links() }}
            </div>
        @else
            <!-- No Notifications Message -->
            <div class="bg-white shadow-sm rounded-lg p-8 text-center">
                <svg class="mx-auto mb-4 h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <p class="text-gray-600 text-lg">You have no notifications.</p>
            </div>
        @endif
    </div>
</div>
@endsection
