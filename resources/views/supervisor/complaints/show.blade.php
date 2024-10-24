<x-app-layout>
    <div class="container my-5">
        <!-- Breadcrumb -->
        <div class="breadcrumb-container mb-4">
            <a href="{{ route('supervisor.complaints.index') }}" class="btn btn-outline-secondary">
                ← Back to Complaints 
            </a>
        </div>

        <!-- Messages Section -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
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
                <div class="image-section p-3 text-center bg-light rounded">
                @if($complaint->getFirstMediaUrl('complaint_images'))
                    <img src="{{ $complaint->getFirstMediaUrl('complaint_images') }}" 
                        alt="Complaint Image" class="img-fluid rounded">
                @else
                    <div class="placeholder-image p-5">No Image Available</div>
                @endif
                </div>
            </div>

            <!-- Details Section -->
            <div class="col-lg-6">
                <div class="details-section p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2>{{ $complaint->comp_desc }}</h2>
                        <span class="text-muted">By: {{ $complaint->officer->name ?? 'N/A' }}</span>
                    </div>
                    <ul class="list-unstyled">
                        <li><strong>Location:</strong> {{ $complaint->comp_location }}</li>
                        <li><strong>Date:</strong> {{ \Carbon\Carbon::parse($complaint->comp_date)->format('d M Y') }}</li>
                        <li><strong>Time:</strong> {{ \Carbon\Carbon::parse($complaint->comp_time)->format('h:i A') }}</li>
                        <li><strong>Status:</strong> <span class="badge bg-info">{{ ucfirst($complaint->comp_status) }}</span></li>
                    </ul>

                    <textarea class="form-control mt-3" rows="3" readonly>{{ $complaint->comp_desc }}</textarea>

                    <h4 class="mt-4">Tasks Included</h4>
                    <div class="task-icons d-flex gap-4 mt-2">
                        @foreach (['Mopping', 'Vacuuming', 'Wiping', 'Organizing'] as $task)
                            <div class="text-center">
                                <img src="/path/to/icon{{ $loop->index + 1 }}.png" alt="{{ $task }}" class="task-icon">
                                <p>{{ $task }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        @if (Auth::user()->role == 'supervisor')
            <div class="assign-section mt-5 p-4 bg-light rounded">
                <h3>Assign Cleaners</h3>
                @if ($complaint->comp_status == 'pending')
                    <p>Available cleaners: <strong>{{ $availableCleaners->count() }}</strong>.</p>
                    
                    <form action="{{ route('assign.cleaner', ['id' => $complaint->id]) }}" method="POST">
                        @csrf
                        <div class="d-flex gap-3 mb-3">
                            <select class="form-select w-50" name="no_of_cleaners" id="no_of_cleaners" required>
                                <option selected disabled>Number of Cleaners</option>
                                @for ($i = 1; $i <= $availableCleaners->count(); $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                            <button type="button" id="proceed-button" class="btn btn-dark">Proceed</button>
                        </div>

                        <div id="cleaner-selection" class="d-none">
                            <label class="form-label">Select Cleaners:</label>
                            <div class="d-flex flex-wrap gap-3">
                                @foreach ($availableCleaners as $cleaner)
                                    <div class="cleaner-item">
                                        @if($cleaner->getFirstMediaUrl('profile_pictures'))
                                            <img src="{{ $cleaner->getFirstMediaUrl('profile_pictures') }}" 
                                                 alt="Profile Picture" class="rounded-circle" width="40" height="40">
                                        @else
                                            <span>No Image</span>
                                        @endif
                                        <input type="checkbox" 
                                               id="cleaner-{{ $cleaner->id }}" 
                                               name="cleaners[]" 
                                               value="{{ $cleaner->id }}">
                                        <label for="cleaner-{{ $cleaner->id }}">{{ $cleaner->cleaner_name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary mt-3">Submit</button>
                    </form>
                @else
                    <div class="alert alert-info mt-3">
                        Complaint is already <strong>{{ ucfirst($complaint->comp_status) }}</strong>.
                    </div>
                    <h4>Assigned Cleaners</h4>
                    <ul class="list-group">
                        @forelse ($complaint->cleaners as $cleaner)
                            <li class="list-group-item">
                                {{ $cleaner->cleaner_name }} 
                                (Assigned by: {{ \App\Models\User::find($cleaner->pivot->assigned_by)->name ?? 'N/A' }},
                                Date: {{ \Carbon\Carbon::parse($cleaner->pivot->assigned_date)->format('d M Y h:i A') }})
                            </li>
                        @empty
                            <li class="list-group-item">No cleaners assigned.</li>
                        @endforelse
                    </ul>
                @endif
            </div>
        @else
            <div class="alert alert-warning mt-5">
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
