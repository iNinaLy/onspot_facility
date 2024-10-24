@extends('layouts.admin')

@section('content')
<div class="container mx-auto my-10 px-4 md:px-6 max-w-screen-xl">
    <!-- Page Title -->
    <div class="flex justify-between items-center mb-8 flex-col md:flex-row">
        <h1 class="text-3xl font-semibold text-gray-900 text-center md:text-left">Manage Officers</h1>

        <!-- Search and Filter Section -->
        <div class="flex items-center space-x-4 mt-4 md:mt-0">
            <!-- Search Form -->
            <form method="GET" action="{{ route('admin.officers') }}" id="filter-form" class="relative flex items-center w-full md:w-80">
                <!-- Search Input with Icon on the Right -->
                <input 
                    type="search" 
                    name="search"
                    class="search-input border-gray-300 rounded-full py-2 px-6 w-full focus:outline-none focus:ring-2 focus:ring-custom-blue shadow-sm transition duration-300 pr-10" 
                    placeholder="Search by name, phone number, or email" 
                    value="{{ request()->query('search') }}"
                    id="search-input"
                    autocomplete="off"
                >

                <!-- Search Icon on the Right -->
                <button type="submit" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 focus:outline-none">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Success Message -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="background-color: #EBF5E1; border-color: #A6D785; padding: 1rem; font-weight: 400;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Officers Table -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden mt-8">
        @if($officers->isEmpty())
            <div class="p-6 text-center text-gray-500">No officers found.</div>
        @else
        <table class="min-w-full table-auto">
            <thead class="bg-custom-blue text-white">
                <tr>
                    <th class="px-6 py-4 text-left">Profile Picture</th>
                    <th class="px-6 py-4 text-left">Name</th>
                    <th class="px-6 py-4 text-left">Email</th>
                    <th class="px-6 py-4 text-left">Phone Number</th>
                    <th class="px-6 py-4 text-left">Building</th>
                    <th class="px-6 py-4 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-pastel-light">
            @foreach($officers as $officer)
                <tr class="border-b hover:bg-pastel-lighter">
                    <!-- Profile Picture Column -->
                    <td class="px-6 py-4">
                        @if ($officer->profile_pic)
                            <img src="{{ asset('storage/' . $officer->profile_pic) }}" alt="Profile Picture" class="w-10 h-10 rounded-full border-2 border-gray-300 object-cover">
                        @else
                            <span class="text-gray-500">No Image</span>
                        @endif
                    </td>

                    <!-- Officer Details -->
                    <td class="px-6 py-4 text-gray-800 font-medium">{{ $officer->name ?? 'Not Available' }}</td>
                    <td class="px-6 py-4 text-gray-800">{{ $officer->email ?? 'Not Available' }}</td>
                    <td class="px-6 py-4 text-gray-800">{{ $officer->phone_no ?? 'Not Available' }}</td>
                    <td class="px-6 py-4 text-gray-800">{{ $officer->building ?? 'Not Set' }}</td>

                    <!-- Actions -->
                    <td class="px-6 py-4">
                        <div class="flex space-x-4 justify-center">
                            <a href="{{ route('admin.officers.edit', $officer->id) }}" 
                               class="text-custom-blue hover:text-custom-blue-dark flex items-center">
                                <i class="bi bi-pencil-square mr-1"></i> Edit
                            </a>
                            <button 
                                type="button" 
                                class="text-custom-red hover:text-red-800 flex items-center" 
                                data-bs-toggle="modal" 
                                data-bs-target="#deleteModal{{ $officer->id }}">
                                <i class="bi bi-trash mr-1"></i> Delete
                            </button>
                        </div>

                        <!-- Delete Confirmation Modal -->
                        <div class="modal fade" id="deleteModal{{ $officer->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $officer->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteModalLabel{{ $officer->id }}">Confirm Deletion</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to delete {{ $officer->name }}?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <form action="{{ route('admin.officers.destroy', $officer->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- End of modal -->
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <!-- Pagination Links -->
        <div class="p-6">
            {{ $officers->appends(request()->query())->links('pagination::tailwind') }}
        </div>


        @endif
    </div>
</div>

<!-- Styling Enhancements -->
<style>
    :root {
        --custom-blue: #2e5675;
        --custom-blue-dark: #23425b;
        --custom-red: #e63946;
    }

    /* Enhanced search input */
    .search-input {
        width: 100%;
        padding-right: 40px;
        border-radius: 9999px;
        padding-left: 16px;
        background: #f0f4f8;
        transition: all 0.3s ease;
    }

    .search-input:focus {
        box-shadow: 0 0 0 4px var(--custom-blue);
        background: white;
    }

    .search-icon-container {
        position: absolute;
        right: 0;
        padding-right: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
    }

    /* Profile picture adjustments */
    .w-10 {
        width: 2.5rem;
        height: 2.5rem;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid #ddd;
    }

    /* Custom modal animation */
    .animate-fade-in {
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    /* Hover effect for table rows */
    tbody tr:hover {
        background-color: #e9effa;
        transition: background-color 0.2s ease;
    }

    /* Button color customization */
    .bg-custom-blue {
        background-color: var(--custom-blue);
    }

    .hover\:bg-custom-blue-dark:hover {
        background-color: var(--custom-blue-dark);
    }

    .text-custom-blue {
        color: var(--custom-blue);
    }

    .hover\:text-custom-blue-dark:hover {
        color: var(--custom-blue-dark);
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        h1 {
            font-size: 1.5rem;
        }
        .table-auto {
            font-size: 0.9rem;
        }
        .w-10 {
            width: 2rem;
            height: 2rem;
        }
    }
</style>

<!-- Script Enhancements -->
<script>
    // When input is cleared, display back all results
    const searchInput = document.getElementById('search-input');
    searchInput.addEventListener('input', function () {
        if (searchInput.value === '') {
            window.location.href = '{{ route('admin.officers') }}';
        }
    });
</script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

@endsection
