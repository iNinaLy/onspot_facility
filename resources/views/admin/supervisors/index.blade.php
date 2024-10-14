@extends('layouts.admin')

@section('content')
<div class="container my-5">
    <h1 class="mb-4 text-center">Manage Supervisors</h1>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    

    <!-- Table to list supervisors -->
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($supervisors as $supervisor)
                    <tr>
                        <td>{{ $supervisor->s_id }}</td>
                        <td>{{ $supervisor->s_name }}</td>
                        <td>{{ $supervisor->s_email }}</td>
                        <td>{{ $supervisor->s_phoneNo }}</td>
                        <td>{{ $supervisor->created_at }}</td>
                        <td>{{ $supervisor->updated_at }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.supervisors.edit', $supervisor->s_id) }}" class="btn btn-outline-warning btn-sm">Edit</a>
                                <form action="{{ route('admin.supervisors.destroy', $supervisor->s_id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this supervisor?')">Delete</button>
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