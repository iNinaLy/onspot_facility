<!-- resources/views/supervisor/cleaners/index.blade.php -->
@extends('layouts.supervisor')

@section('content')
<div class="container">
    <h1>Manage Cleaners (Supervisor)</h1>
    
    <!-- Display total, available, and unavailable cleaner counts -->
    <div class="mt-4">
        <p>Total Cleaners: {{ $totalCleaners }}</p>
        <p>Available Cleaners: {{ $availableCount }}</p>
        <p>Unavailable Cleaners: {{ $unavailableCount }}</p> <!-- Use the $unavailableCount variable -->
    </div>

    <!-- Table to list cleaners -->
    <table class="table table-bordered mt-4">
        <thead>
            <tr>
                <th>Name</th>
                <th>Phone Number</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cleaners as $cleaner)
                <tr>
                    <td>{{ $cleaner->cleaner_name }}</td>
                    <td>{{ $cleaner->cleaner_phoneNo }}</td>
                    <td>
                        @if($cleaner->status === 'available')
                            <span class="badge bg-success">Available</span>
                        @else
                            <span class="badge bg-danger">Unavailable</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('supervisor.cleaners.show', $cleaner->id) }}" class="btn btn-info btn-sm">View</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
