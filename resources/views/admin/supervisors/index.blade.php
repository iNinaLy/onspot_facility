<!-- resources/views/admin/supervisors/index.blade.php -->

@extends('layouts.admin')

@section('content')
<style>
    /* Search Bar Styling */
    .search-bar {
        position: relative;
        margin-bottom: 30px;
    }

    .search-bar input {
        padding: 10px 40px 10px 20px; /* Added padding-right for the icon */
        width: 100%;
        border: 2px solid #ced4da;
        border-radius: 30px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        font-size: 16px;
        transition: all 0.3s ease-in-out;
    }

    .search-bar input:focus {
        outline: none;
        border-color: #276678;
        box-shadow: 0 0 10px rgba(39, 102, 120, 0.5);
    }

    .search-bar .fa-search {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #276678;
        cursor: pointer;
        font-size: 18px;
        transition: color 0.3s ease;
    }

    .search-bar .fa-search:hover {
        color: #1e5161;
    }

    /* Table Styling */
    .table thead {
        background-color: #f7f7f7;
        font-weight: bold;
    }

    .table tbody tr:hover {
        background-color: #f1f1f1;
    }

    /* Button Styling */
    .btn-edit, .btn-delete {
        padding: 5px 10px;
        font-size: 14px;
        border-radius: 5px;
    }

    .btn-edit {
        background-color: white;
        color: #276678;
        border: 1px solid #276678;
    }

    .btn-edit:hover {
        background-color: #276678;
        color: white;
    }

    .btn-delete {
        background-color: black;
        color: white;
        border: none;
    }

    .btn-delete:hover {
        background-color: #555;
    }

    .no-results {
        text-align: center;
        color: #888;
        font-size: 16px;
        padding: 20px 0;
    }

    /* Responsive Adjustments */
    @media (max-width: 576px) {
        .search-bar input {
            font-size: 14px;
        }

        .search-bar .fa-search {
            font-size: 16px;
        }
    }
</style>

<div class="container my-5 px-5">
    <h1 class="mb-4 text-center">Manage Supervisors</h1>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Search Bar -->
    <div class="search-bar">
        <input type="text" id="search" class="form-control" placeholder="Search by name, email, or phone number" autocomplete="off">
        <i class="fas fa-search"></i>
    </div>

    <!-- Table to list supervisors -->
    <div class="table-responsive" id="supervisorTable">
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
            <tbody id="supervisorResults">
                <!-- Initially display all supervisors -->
                @foreach($supervisors as $supervisor)
                    <tr>
                        <td>{{ $supervisor->s_id }}</td>
                        <td>{{ $supervisor->s_name }}</td>
                        <td>{{ $supervisor->s_email }}</td>
                        <td>{{ $supervisor->s_phoneNo }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.supervisors.edit', $supervisor->s_id) }}" class="btn btn-edit btn-sm">Edit</a>
                                <form action="{{ route('admin.supervisors.destroy', $supervisor->s_id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-delete btn-sm" onclick="return confirm('Are you sure you want to delete this supervisor?')">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Optional: Display a message when no supervisors are available initially -->
        @if($supervisors->isEmpty())
            <div class="no-results">No supervisors available.</div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('search');
        const supervisorResults = document.getElementById('supervisorResults');

        searchInput.addEventListener('input', function () {
            const query = this.value.trim();

            if (query.length > 2) { // Start search after typing 3 characters
                fetch(`/admin/supervisors/search?query=${encodeURIComponent(query)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    supervisorResults.innerHTML = ''; // Clear existing results

                    if (data.supervisors.length > 0) {
                        data.supervisors.forEach(supervisor => {
                            const row = document.createElement('tr');

                            row.innerHTML = `
                                <td>${supervisor.s_id}</td>
                                <td>${supervisor.s_name}</td>
                                <td>${supervisor.s_email}</td>
                                <td>${supervisor.s_phoneNo}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="/admin/supervisors/${supervisor.s_id}/edit" class="btn btn-edit btn-sm">Edit</a>
                                        <form action="/admin/supervisors/${supervisor.s_id}" method="POST" style="display:inline-block;">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn btn-delete btn-sm" onclick="return confirm('Are you sure you want to delete this supervisor?')">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            `;
                            supervisorResults.appendChild(row);
                        });
                    } else {
                        // If no results, show message
                        supervisorResults.innerHTML = '<tr><td colspan="5" class="no-results">No supervisors found.</td></tr>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching search results:', error);
                    supervisorResults.innerHTML = '<tr><td colspan="5" class="no-results">An error occurred while searching.</td></tr>';
                });
            } else {
                // If query is too short, optionally display all supervisors or clear the table
                if (query.length === 0) {
                    // Optionally, you can reset the table to show all supervisors
                    // Uncomment the following lines if you want to fetch all supervisors when the search input is cleared

                    /*
                    fetch(`/admin/supervisors/search?query=`)
                        .then(response => response.json())
                        .then(data => {
                            supervisorResults.innerHTML = ''; // Clear existing results

                            if (data.supervisors.length > 0) {
                                data.supervisors.forEach(supervisor => {
                                    const row = document.createElement('tr');

                                    row.innerHTML = `
                                        <td>${supervisor.s_id}</td>
                                        <td>${supervisor.s_name}</td>
                                        <td>${supervisor.s_email}</td>
                                        <td>${supervisor.s_phoneNo}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="/admin/supervisors/${supervisor.s_id}/edit" class="btn btn-edit btn-sm">Edit</a>
                                                <form action="/admin/supervisors/${supervisor.s_id}" method="POST" style="display:inline-block;">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <button type="submit" class="btn btn-delete btn-sm" onclick="return confirm('Are you sure you want to delete this supervisor?')">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    `;
                                    supervisorResults.appendChild(row);
                                });
                            } else {
                                supervisorResults.innerHTML = '<tr><td colspan="5" class="no-results">No supervisors available.</td></tr>';
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching supervisors:', error);
                            supervisorResults.innerHTML = '<tr><td colspan="5" class="no-results">An error occurred while fetching supervisors.</td></tr>';
                        });
                    */
                } else {
                    // Clear the results if query is too short
                    supervisorResults.innerHTML = '';
                }
            }
        });
    });
</script>
@endsection
