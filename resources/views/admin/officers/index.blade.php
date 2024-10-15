@extends('layouts.admin')

@section('content')
<style>
    /* General Styles */
    body {
        font-family: 'Helvetica Neue', Arial, sans-serif;
    }

    /* Button Hover Effect */
    .btn:hover {
        background-color: #276678; /* Change to a different color on hover */
        color: #fff; /* Change text color to white */
        border-color: #276678; /* Match border color with the background */
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    /* Edit Button Styles */
    .btn-edit {
        background-color: white;
        color: #276678; /* Match the text color with the theme */
        border: 1px solid #276678; /* Border to match the text */
        margin-right: 10px; /* Add margin to the right for spacing */
    }

    /* Delete Button Styles */
    .btn-delete {
        background-color: black;
        color: white; /* Text color for delete button */
    }

    /* Table Styles */
    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        padding: 1rem;
        font-weight: 600;
        color: #333;
        background-color: #F7F7F7;
        border-bottom: 2px solid #EAEAEA;
    }

    td {
        padding: 1rem;
        font-weight: 400;
        border-bottom: 1px solid #EAEAEA;
        transition: background-color 0.3s;
    }

    tr:hover {
        background-color: #f1f1f1; /* Light gray on hover */
    }

    /* Modal Styles */
    .modal-content {
        border-radius: 12px;
        border: none;
    }

    .modal-header {
        border-bottom: none;
    }
</style>

<div class="container my-5">
    <h1 class="mb-4 text-center">Manage Officers</h1>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Search Bar -->
    <div class="mb-3">
        <input type="text" id="search" class="form-control" placeholder="Search by name, email, or phone number" autocomplete="off">
    </div>

    <!-- Table to list officers -->
    <div class="table-responsive" id="officerTable">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="officerResults">
                @foreach($officers as $officer)
                    <tr>
                        <td>{{ $officer->id }}</td>
                        <td>{{ $officer->officer_name }}</td>
                        <td>{{ $officer->officer_email }}</td>
                        <td>{{ $officer->officer_phoneNo }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.officers.edit', $officer->id) }}" class="btn btn-edit btn-sm">Edit</a>
                                <form action="{{ route('admin.officers.destroy', $officer->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-delete btn-sm" onclick="return confirm('Are you sure you want to delete this officer?')">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    document.getElementById('search').addEventListener('input', function() {
        let query = this.value;

        if (query.length > 2) { // Start search after typing 2 characters
            fetch(`/admin/officers/search?query=${query}`)
                .then(response => response.json())
                .then(data => {
                    let officerResults = document.getElementById('officerResults');
                    officerResults.innerHTML = ''; // Clear existing results

                    if (data.officers.length > 0) {
                        data.officers.forEach(officer => {
                            let row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${officer.id}</td>
                                <td>${officer.officer_name}</td>
                                <td>${officer.officer_email}</td>
                                <td>${officer.officer_phoneNo}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="/admin/officers/${officer.id}/edit" class="btn btn-edit btn-sm">Edit</a>
                                        <form action="/admin/officers/${officer.id}" method="POST" style="display:inline-block;">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn btn-delete btn-sm">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            `;
                            officerResults.appendChild(row);
                        });
                    } else {
                        officerResults.innerHTML = '<tr><td colspan="5" class="text-center">No officers found</td></tr>';
                    }
                });
        } else {
            document.getElementById('officerResults').innerHTML = ''; // Clear the results if query is too short
        }
    });
</script>
@endsection
