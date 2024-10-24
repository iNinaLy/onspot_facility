@extends('layouts.admin')

@section('content')
<div class="container mx-auto my-10 px-4 md:px-6 max-w-screen-xl">
    <!-- Page Title -->
    <div class="flex justify-between items-center mb-8 flex-col md:flex-row">
        <h1 class="text-3xl font-semibold text-gray-900 text-center md:text-left">Manage Cleaners</h1>

        <!-- Search and Filter Section -->
        <div class="flex items-center space-x-4 mt-4 md:mt-0">
            <!-- Search Form -->
            <form method="GET" action="{{ route('admin.cleaners') }}" id="filter-form" class="relative flex items-center w-full md:w-80">
                <!-- Search Input with Icon on the Right -->
                <input 
                    type="search" 
                    name="search"
                    class="search-input border-gray-300 rounded-full py-2 px-6 w-full focus:outline-none focus:ring-2 focus:ring-custom-blue shadow-sm transition duration-300 pr-10" 
                    placeholder="Search cleaners..." 
                    value="{{ request()->query('search') }}"
                    id="search-input"
                    autocomplete="off"
                >

                <!-- Search Icon on the Right -->
                <button type="submit" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 focus:outline-none">
                    <i class="fas fa-search"></i>
                </button>
            </form>

            <!-- Filter Button -->
            <button id="filter-button" class="bg-custom-blue text-white px-4 py-2 rounded-full shadow hover:bg-custom-blue-dark focus:outline-none focus:ring-2 focus:ring-custom-blue">
               Filter
            </button>
        </div>
    </div>

    <!-- Filter Modal -->
    <div id="filter-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative animate-fade-in">
            <h2 class="text-2xl font-semibold mb-4">Filter Cleaners</h2>
            <form id="filter-form" method="GET" action="{{ route('admin.cleaners') }}">
                <!-- Preserve search input in the filter form -->
                <input type="hidden" name="search" value="{{ request()->get('search') }}">

                <!-- Status Filter -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Status</label>
                    <select name="status" class="w-full border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-custom-blue shadow-sm">
                        <option value="">All</option>
                        <option value="available" {{ request()->get('status') == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="unavailable" {{ request()->get('status') == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" id="filter-close" class="bg-gray-300 px-4 py-2 rounded-lg">Cancel</button>
                    <button type="submit" class="bg-custom-blue text-white px-4 py-2 rounded-lg hover:bg-custom-blue-dark">Apply</button>
                </div>
            </form>
            <button id="filter-close-button" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <!-- Cleaners Table -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden mt-8">
        @if($cleaners->isEmpty())
            <div class="p-6 text-center text-gray-500">No cleaners found.</div>
        @else
        <table class="min-w-full table-auto">
            <thead class="bg-custom-blue text-white">
                <tr>
                    <th class="px-6 py-4 text-left">Profile</th>
                    <th class="px-6 py-4 text-left">Name</th>
                    <th class="px-6 py-4 text-left">Phone Number</th>
                    <th class="px-6 py-4 text-left">Username</th>
                    <th class="px-6 py-4 text-left">Status</th>
                    <th class="px-6 py-4 text-left">Building</th>
                    <th class="px-6 py-4 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-pastel-light">
                @foreach($cleaners as $cleaner)
                    <tr class="border-b hover:bg-pastel-lighter">
                        <td class="px-6 py-4 flex items-center">
                            @if($cleaner->profile_pic)
                                <img src="data:image/jpeg;base64,{{ base64_encode($cleaner->profile_pic) }}" 
                                     alt="Profile Picture" class="w-10 h-10 rounded-full border-2 border-gray-300 object-cover">
                            @else
                                <span class="text-gray-500">No Image</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-800 font-medium">{{ $cleaner->cleaner_name }}</td>
                        <td class="px-6 py-4 text-gray-800">{{ $cleaner->cleaner_phoneNo }}</td>
                        <td class="px-6 py-4 text-gray-800">{{ $cleaner->cleaner_username }}</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-sm font-medium 
                                @if($cleaner->status == 'available')
                                    bg-green-200 text-green-800
                                @elseif($cleaner->status == 'unavailable')
                                    bg-red-200 text-red-800
                                @else
                                    bg-gray-200 text-gray-800
                                @endif
                            ">
                                {{ ucfirst($cleaner->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-800">{{ $cleaner->building ?? 'Not Assigned' }}</td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-4">
                                <a href="{{ route('admin.cleaners.edit', $cleaner->id) }}" 
                                   class="text-custom-blue hover:text-custom-blue-dark flex items-center">
                                    <i class="bi bi-pencil-square mr-1"></i> Edit
                                </a>
                                <button 
                                    type="button" 
                                    class="text-custom-red hover:text-red-800 flex items-center" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#deleteModal{{ $cleaner->id }}">
                                    <i class="bi bi-trash mr-1"></i> Delete
                                </button>
                            </div>

                            <!-- Delete Confirmation Modal -->
                            <div class="modal fade" id="deleteModal{{ $cleaner->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $cleaner->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteModalLabel{{ $cleaner->id }}">Confirm Deletion</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Are you sure you want to delete <strong>{{ $cleaner->cleaner_name }}</strong>?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <form action="{{ route('admin.cleaners.destroy', $cleaner->id) }}" method="POST">
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
            {{ $cleaners->appends(request()->query())->links('pagination::tailwind') }}
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
    // JavaScript to handle filter modal display with smooth transitions
    document.getElementById('filter-button').addEventListener('click', function() {
        document.getElementById('filter-modal').classList.remove('hidden');
    });

    document.getElementById('filter-close').addEventListener('click', function() {
        document.getElementById('filter-modal').classList.add('hidden');
    });

    document.getElementById('filter-close-button').addEventListener('click', function() {
        document.getElementById('filter-modal').classList.add('hidden');
    });

    // When input is cleared, display back all results
    const searchInput = document.getElementById('search-input');
    searchInput.addEventListener('input', function () {
        if (searchInput.value === '') {
            window.location.href = '{{ route('admin.cleaners') }}';
        }
    });
</script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

@endsection
