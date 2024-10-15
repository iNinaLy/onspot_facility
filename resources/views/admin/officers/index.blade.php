@extends('layouts.admin')

@section('content')
<div class="container my-5">
    <h1 class="mb-4 text-center">Manage Officers</h1>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif


    <!-- Search Bar with Suggestions -->
    <div class="mb-3 position-relative">
        <input type="text" id="search" class="form-control" placeholder="Search by name">
        <div id="suggestions" class="list-group position-absolute w-100" style="z-index: 1000;"></div>
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
                    <th>Created At</th>
                    <th>Updated At</th>
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
                        <td>{{ $officer->created_at }}</td>
                        <td>{{ $officer->updated_at }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.officers.edit', $officer->id) }}" class="btn btn-outline-warning btn-sm">Edit</a>
                                <form action="{{ route('admin.officers.destroy', $officer->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this officer?')">Delete</button>
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
                    let suggestionsBox = document.getElementById('suggestions');
                    suggestionsBox.innerHTML = ''; // Clear previous suggestions

                    if (data.officers.length > 0) {
                        data.officers.forEach(officer => {
                            let item = document.createElement('a');
                            item.href = '#';
                            item.classList.add('list-group-item', 'list-group-item-action');
                            item.textContent = officer.officer_name;
                            item.onclick = function() {
                                displayOfficerResults(data.officers);
                                suggestionsBox.innerHTML = ''; // Clear suggestions
                            };
                            suggestionsBox.appendChild(item);
                        });
                    } else {
                        let noResult = document.createElement('div');
                        noResult.classList.add('list-group-item', 'text-muted');
                        noResult.textContent = 'No officers found';
                        suggestionsBox.appendChild(noResult);
                    }
                });
        } else {
            document.getElementById('suggestions').innerHTML = ''; // Clear suggestions if query is short
        }
    });

    function displayOfficerResults(officers) {
        let officerResults = document.getElementById('officerResults');
        officerResults.innerHTML = ''; // Clear existing results

        if (officers.length > 0) {
            officers.forEach(officer => {
                let row = document.createElement('tr');
                row.innerHTML = `
                    <td>${officer.id}</td>
                    <td>${officer.officer_name}</td>
                    <td>${officer.officer_email}</td>
                    <td>${officer.officer_phoneNo}</td>
                    <td>${officer.created_at}</td>
                    <td>${officer.updated_at}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="/admin/officers/${officer.id}/edit" class="btn btn-outline-warning btn-sm">Edit</a>
                            <form action="/admin/officers/${officer.id}" method="POST" style="display:inline-block;">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    </td>
                `;
                officerResults.appendChild(row);
            });
        } else {
            officerResults.innerHTML = '<tr><td colspan="7" class="text-center">No officers found</td></tr>';
        }
    }
</script>
@endsection
