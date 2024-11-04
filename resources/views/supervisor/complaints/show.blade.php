<x-app-layout>
    <div class="container my-5">
        <!-- Breadcrumb with Back Icon -->
        <div class="d-flex align-items-center mb-4">
            <a href="{{ route('supervisor.complaints.index') }}" class="me-2 text-decoration-none" style="color: #2e5675;">
                <i class="bi bi-arrow-left-circle" style="font-size: 1.5rem;"></i>
            </a>
            <h6 class="text-muted m-0">Complaints / Details</h6>
        </div>

        <!-- Messages Section -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row gx-5">
            <!-- Image Section -->
            <div class="col-lg-6">
                <div class="image-section bg-light rounded-3 shadow-sm p-3 text-center" style="height: 300px; display: flex; align-items: center; justify-content: center;">
                    @if($complaint->getFirstMediaUrl('complaint_images'))
                        <img src="{{ $complaint->getFirstMediaUrl('complaint_images') }}" 
                             alt="Complaint Image" class="img-fluid rounded-3" style="max-height: 100%; object-fit: cover;">
                    @else
                        <div class="placeholder-image text-muted">No Image Available</div>
                    @endif
                </div>
            </div>

            <!-- Details Section -->
            <div class="col-lg-6">
                <div class="details-section bg-white rounded-3 shadow-sm p-4">
                    <h4 class="mb-3 fw-bold" style="color: #2e5675;">{{ $complaint->comp_desc }}</h4>
                    <ul class="list-unstyled mb-3">
                        <li><strong>Location:</strong> {{ $complaint->comp_location }}</li>
                        <li><strong>Date:</strong> {{ \Carbon\Carbon::parse($complaint->comp_date)->format('d M Y') }}</li>
                        <li><strong>Time:</strong> {{ \Carbon\Carbon::parse($complaint->comp_time)->format('h:i A') }}</li>
                        <li><strong>Status:</strong> 
                            <span class="badge rounded-pill 
                                         {{ $complaint->comp_status == 'completed' ? 'bg-success' : ($complaint->comp_status == 'ongoing' ? 'bg-primary' : 'bg-secondary') }}">
                                {{ ucfirst($complaint->comp_status) }}
                            </span>
                        </li>
                    </ul>

                    <div class="bg-light p-3 rounded-3 mb-3">
                        <textarea class="form-control bg-transparent border-0" rows="3" readonly>{{ $complaint->comp_desc }}</textarea>
                    </div>

                    <h5 class="fw-bold mt-4">Tasks Included</h5>
                    <div class="task-icons d-flex justify-content-between bg-light p-3 rounded-3 mt-2">
                        @foreach (['Mopping', 'Vacuuming', 'Wiping', 'Organizing'] as $task)
                            <div class="text-center">
                                <img src="/path/to/icon{{ $loop->index + 1 }}.png" alt="{{ $task }}" style="width: 40px; height: 40px;">
                                <p class="small text-muted mt-2">{{ $task }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        @if (Auth::user()->role == 'supervisor')
            <div class="assign-section mt-5 p-4 bg-white rounded-3 shadow-sm">
                <h4 class="mb-4" style="color: #2e5675;">Assign Cleaners</h4>
                
                @if ($complaint->comp_status == 'pending')
                    <p class="text-muted mb-4">Available cleaners: <strong>{{ $availableCleaners->count() }}</strong></p>
                    
                    <form action="{{ route('supervisor.assign.cleaner', ['id' => $complaint->id]) }}" method="POST">
                        @csrf
                        <!-- Number of Cleaners Selection -->
                        <div class="d-flex gap-3 mb-4 align-items-center">
                            <label for="no_of_cleaners" class="form-label mb-0" style="font-weight: 500;">Number of Cleaners:</label>
                            <select class="form-select w-25" name="no_of_cleaners" id="no_of_cleaners" required>
                                <option selected disabled>Number of cleaners</option>
                                @for ($i = 1; $i <= $availableCleaners->count(); $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                            <button type="button" id="proceed-button" class="btn btn-primary" style="background-color: #2e5675; border-color: #2e5675;">Proceed</button>
                        </div>

                        <!-- Cleaner Selection (Hidden initially) -->
                        <div id="cleaner-selection" class="d-none">
                            <label class="form-label mb-3" style="font-weight: 500;">Select Cleaners:</label>
                            <div class="d-flex flex-wrap gap-3">
                                @foreach ($availableCleaners as $cleaner)
                                    <div class="cleaner-item text-center d-flex flex-column align-items-center">
                                        <!-- Profile Picture -->
                                        <div class="profile-picture-container mb-2 position-relative" style="width: 60px; height: 60px; overflow: hidden; border-radius: 50%; border: 2px solid #e9ecef;">
                                            @if($cleaner->getFirstMediaUrl('profile_pictures'))
                                                <img src="{{ $cleaner->getFirstMediaUrl('profile_pictures') }}" 
                                                     alt="Profile Picture" class="img-fluid" style="width: 100%; height: 100%; object-fit: cover;">
                                            @else
                                                <div class="d-flex align-items-center justify-content-center h-100 text-muted" style="background-color: #f8f9fa;">
                                                    <i class="bi bi-person-fill" style="font-size: 1.5rem;"></i>
                                                </div>
                                            @endif
                                            <!-- Overlay Checkbox -->
                                            <input type="checkbox" 
                                                   id="cleaner-{{ $cleaner->id }}" 
                                                   name="cleaners[]" 
                                                   value="{{ $cleaner->id }}" 
                                                   class="form-check-input position-absolute bottom-0 end-0 m-1"
                                                   style="background-color: #ffffff; border-color: #ced4da;">
                                        </div>
                                        <label for="cleaner-{{ $cleaner->id }}" class="small text-muted">{{ $cleaner->cleaner_name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary mt-4" style="background-color: #2e5675; border-color: #2e5675;">Submit</button>
                    </form>
                @else
                    <div class="alert alert-info mt-3 rounded-3 shadow-sm">
                        Complaint is already <strong>{{ ucfirst($complaint->comp_status) }}</strong>.
                    </div>
                    <h5 class="mt-4">Assigned Cleaners</h5>
                    <ul class="list-group list-group-flush rounded-3 shadow-sm">
                        @forelse ($complaint->cleaners as $cleaner)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $cleaner->cleaner_name }} 
                                <span class="text-muted small">Assigned by: {{ \App\Models\User::find($cleaner->pivot->assigned_by)->name ?? 'N/A' }},
                                Date: {{ \Carbon\Carbon::parse($cleaner->pivot->assigned_date)->format('d M Y h:i A') }}</span>
                            </li>
                        @empty
                            <li class="list-group-item text-muted">No cleaners assigned.</li>
                        @endforelse
                    </ul>
                @endif
            </div>
        @else
            <div class="alert alert-warning mt-5 rounded-3 shadow-sm">
                You do not have permission to assign cleaners. Only supervisors can assign cleaners.
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const proceedButton = document.getElementById('proceed-button');
            const cleanerSelection = document.getElementById('cleaner-selection');
            const cleanerCheckboxes = document.querySelectorAll('#cleaner-selection input[type="checkbox"]');

            proceedButton.addEventListener('click', () => {
                const selectedNumber = parseInt(document.getElementById('no_of_cleaners').value);

                if (!selectedNumber) {
                    alert('Please select the number of cleaners.');
                    return;
                }

                cleanerSelection.classList.remove('d-none');
                cleanerCheckboxes.forEach(checkbox => checkbox.checked = false);

                cleanerCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', () => {
                        if (Array.from(cleanerCheckboxes).filter(c => c.checked).length > selectedNumber) {
                            checkbox.checked = false;
                            alert(`You can only select ${selectedNumber} cleaner(s).`);
                        }
                    });
                });
            });
        });
    </script>
</x-app-layout>
