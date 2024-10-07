@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Manage Cleaners (Admin)</h1>
    <a href="{{ route('admin.cleaners.create') }}" class="btn btn-primary">Add Cleaner</a>

    <table class="table table-bordered mt-4">
        <thead>
            <tr>
                <th>Name</th>
                <th>Phone Number</th>
                <th>Availability</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cleaners as $cleaner)
                <tr>
                    <td>{{ $cleaner->cleaner_name }}</td>
                    <td>{{ $cleaner->cleaner_phoneNo }}</td>
                    <td>
                        @if($cleaner->cleaner_available)
                            <span class="badge bg-success">Available</span>
                        @else
                            <span class="badge bg-danger">Unavailable</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.cleaners.edit', $cleaner->cleaner_id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('admin.cleaners.destroy', $cleaner->cleaner_id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
