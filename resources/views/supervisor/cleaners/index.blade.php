@extends('layouts.app')

@section('title', 'Cleaners Management')

@push('styles')
<style>
    :root {
        --primary-color: #2E5675;
        --secondary-color: #4caf50;
        --available-color: #2196f3;
        --unavailable-color: #f44336;
    }

    /* General Container Styles */
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }

    /* Header with Filters on the Right */
    .header-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        margin-top: 3rem;
    }

    .heading {
        font-size: 2rem;
        font-weight: bold;
        color: var(--primary-color);
    }

    /* Filter Form Styles */
    .filter-container {
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .filter-container select,
    .filter-container input {
        padding: 0.5rem;
        border-radius: 0.5rem;
        border: 1px solid #d1d5db;
        font-size: 0.875rem;
        width: 150px;
    }

    .view-details-btn {
        background-color: var(--primary-color);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-weight: 600;
        transition: background-color 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .view-details-btn:hover {
        background-color: #1f3c52;
    }

    /* Cleaner Overview Section */
    .cleaner-overview {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
    }

    .overview-box {
        flex: 1;
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(5px);
        border-radius: 15px;
        padding: 15px 20px;
        text-align: center;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .overview-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .overview-number {
        font-size: 1.6rem;
        font-weight: bold;
    }

    .overview-label {
        font-size: 0.9rem;
        font-weight: 500;
        color: #555;
    }

    .overview-total .overview-number {
        color: var(--secondary-color);
    }

    .overview-available .overview-number {
        color: var(--available-color);
    }

    .overview-unavailable .overview-number {
        color: var(--unavailable-color);
    }

    /* Cleaner Card Styling */
    .cleaner-card {
        width: 220px;
        background: #ffffff;
        border-radius: 15px;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        padding: 25px;
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .cleaner-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .profile-pic {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 15px;
        border: 4px solid var(--available-color);
        box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
    }

    .cleaner-name {
        font-size: 1.15rem;
        font-weight: 600;
        color: #1f2937;
        margin: 10px 0;
    }

    .cleaner-status {
        font-size: 0.9rem;
        font-weight: 500;
        padding: 5px 15px;
        border-radius: 15px;
        display: inline-block;
        margin-top: 8px;
    }

    .status-available {
        background-color: #e3fcec;
        color: var(--secondary-color);
    }

    .status-unavailable {
        background-color: #fdecea;
        color: var(--unavailable-color);
    }

    .grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 30px;
        justify-items: center;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="header-container">
        <h1 class="heading">Cleaners</h1>
        <form id="filter-form" class="filter-container">
            <input 
                type="search" 
                class="search-input" 
                placeholder="Search for cleaners..." 
                aria-label="Search" 
                id="search-input" 
            />
            <select id="sort-status" class="sort-select">
                <option value="all">All Status</option>
                <option value="available">Available</option>
                <option value="unavailable">Unavailable</option>
            </select>
        </form>
    </div>

    <div class="cleaner-overview">
        <div class="overview-box overview-total">
            <div class="overview-number">{{ $totalCleaners }}</div>
            <div class="overview-label">Total Cleaners</div>
        </div>
        <div class="overview-box overview-available">
            <div class="overview-number">{{ $availableCount }}</div>
            <div class="overview-label">Available</div>
        </div>
        <div class="overview-box overview-unavailable">
            <div class="overview-number">{{ $unavailableCount }}</div>
            <div class="overview-label">Unavailable</div>
        </div>
    </div>

    <div class="grid" id="cleaner-grid">
        @foreach($cleaners as $cleaner)
        <div class="cleaner-card" data-name="{{ strtolower($cleaner->cleaner_name) }}" data-status="{{ strtolower($cleaner->status) }}">
            @if($cleaner->profile_pic)
                <img src="data:image/jpeg;base64,{{ base64_encode($cleaner->profile_pic) }}" alt="Cleaner Profile Picture" class="profile-pic">
            @else
                <img src="{{ asset('images/default-placeholder.png') }}" alt="Cleaner Profile Picture" class="profile-pic">
            @endif
            <p class="cleaner-name">{{ $cleaner->cleaner_name }}</p>
            <p class="cleaner-status {{ $cleaner->status == 'available' ? 'status-available' : 'status-unavailable' }}">
                {{ ucfirst($cleaner->status) }}
            </p> 
        </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script>
    const searchInput = document.getElementById('search-input');
    const statusFilter = document.getElementById('sort-status');

    function filterCleaners() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedStatus = statusFilter.value;
        document.querySelectorAll('.cleaner-card').forEach(card => {
            const name = card.getAttribute('data-name');
            const status = card.getAttribute('data-status');
            const matchesSearch = name.includes(searchTerm);
            const matchesStatus = selectedStatus === 'all' || status === selectedStatus;
            card.style.display = matchesSearch && matchesStatus ? 'flex' : 'none';
        });
    }

    searchInput.addEventListener('input', filterCleaners);
    statusFilter.addEventListener('change', filterCleaners);
</script>
@endpush
