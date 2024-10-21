@extends('layouts.admin')

@section('content')
<div class="container my-5">
    <h1 class="mb-4 text-center">Manage Cleaners</h1>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif


    <!-- Table to list cleaners -->
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Phone Number</th>
                    <th>Username</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cleaners as $cleaner)
                    <tr>
                        <td>{{ $cleaner->cleaner_name }}</td>
                        <td>{{ $cleaner->cleaner_phoneNo }}</td>
                        <td>{{ $cleaner->username }}</td>
                        <td>
                            <span class="badge {{ $cleaner->status == 'Active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ $cleaner->status }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.cleaners.edit', $cleaner->id) }}" class="btn btn-outline-warning btn-sm">Edit</a>
                                <form action="{{ route('admin.cleaners.destroy', $cleaner->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this cleaner?')">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection