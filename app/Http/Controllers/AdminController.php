<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Supervisor;
use App\Models\Complaint;
use App\Models\Cleaner;
use App\Models\Officer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function dashboard()
    {
        $totalComplaints = Complaint::count();
        $activeCleaners = Cleaner::where('status', 'available')->count();
        $totalOfficers = User::where('role', 'officer')->count();
        $totalSupervisors = User::where('role', 'supervisor')->count();

        $recentComplaints = Complaint::latest()->take(5)->get(['id', 'comp_date', 'comp_time', 'comp_desc', 'comp_location', 'comp_status']);
        $monthlyComplaints = Complaint::selectRaw("MONTH(comp_date) as month, COUNT(*) as count")
            ->groupBy('month')
            ->pluck('count', 'month');

        $cleanersByStatus = Cleaner::selectRaw("status, COUNT(*) as count")->groupBy('status')->pluck('count', 'status');
        $complaintsByStatus = Complaint::selectRaw("comp_status, COUNT(*) as count")->groupBy('comp_status')->pluck('count', 'comp_status');

        return view('admin.dashboard', compact(
            'totalComplaints', 'activeCleaners', 'totalOfficers', 'totalSupervisors', 'recentComplaints',
            'monthlyComplaints', 'cleanersByStatus', 'complaintsByStatus'
        ));
    }

    /**
     * Show the admin profile edit form.
     */
    public function editProfile()
    {
        return view('admin.profile.edit');
    }

    /**
     * Update the admin's profile information.
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'password' => 'nullable|confirmed|min:8',
        ]);

        $admin = User::findOrFail(Auth::id());
        $admin->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->filled('password') ? Hash::make($request->password) : $admin->password,
        ]);

        return redirect()->route('admin.profile.edit')->with('success', 'Profile updated successfully!');
    }


    /**
     * List and filter complaints.
     */
    public function complaints(Request $request)
    {
        $status = $request->input('status');
        $complaints = Complaint::when($status, fn($query) => $query->where('comp_status', $status))
            ->latest('comp_date')
            ->paginate(10);

        return view('admin.complaints.index', compact('complaints', 'status'));
    }

    /**
     * Display form to create a complaint.
     */
    public function createComplaint()
    {
        return view('admin.complaints.create');
    }

    /**
     * Store a newly created complaint.
     */
    public function storeComplaint(Request $request)
    {
        $this->validateComplaint($request);
        Complaint::create($request->all());
        return redirect()->route('admin.complaints.index')->with('success', 'Complaint created successfully!');
    }

    /**
     * Edit a complaint.
     */
    public function editComplaint(Complaint $complaint)
    {
        return view('admin.complaints.edit', compact('complaint'));
    }

    /**
     * Update a complaint.
     */
    public function updateComplaint(Request $request, Complaint $complaint)
    {
        $this->validateComplaint($request);
        $complaint->update($request->all());
        return redirect()->route('admin.complaints.index')->with('success', 'Complaint updated successfully!');
    }

    /**
     * Delete a complaint.
     */
    public function destroyComplaint(Complaint $complaint)
    {
        $complaint->delete();
        return redirect()->route('admin.complaints.index')->with('success', 'Complaint deleted successfully!');
    }

    /**
     * Search complaints by description, location, or ID.
     */
    public function searchComplaints(Request $request)
    {
        $query = $request->input('query');
        $complaints = Complaint::where('comp_desc', 'like', "%{$query}%")
            ->orWhere('comp_location', 'like', "%{$query}%")
            ->orWhere('id', 'like', "%{$query}%")
            ->paginate(10);

        return view('admin.complaints.index', compact('complaints', 'query'));
    }

    /**
     * Validate complaint data.
     */
    private function validateComplaint(Request $request)
    {
        $request->validate([
            'comp_desc' => 'required|string|max:255',
            'comp_status' => 'required|in:pending,on going,completed',
            'comp_location' => 'required|string|max:255',
            'comp_date' => 'required|date',
            'comp_time' => 'required|date_format:H:i',
        ]);
    }

    /**
     * List and search cleaners.
     */
    public function cleaners(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status');

        $cleaners = Cleaner::when($search, fn($query) => $query->where(function ($query) use ($search) {
            $query->where('cleaner_name', 'LIKE', "%{$search}%")
                ->orWhere('cleaner_phoneNo', 'LIKE', "%{$search}%")
                ->orWhere('cleaner_username', 'LIKE', "%{$search}%");
        }))
            ->when($status, fn($query) => $query->where('status', strtolower($status)))
            ->paginate(10);

        return view('admin.cleaners.index', compact('cleaners', 'search', 'status'));
    }

    /**
     * Display form to create a cleaner.
     */
    public function createCleaner()
    {
        return view('admin.cleaners.create');
    }

    /**
     * Store a newly created cleaner.
     */
    public function storeCleaner(Request $request)
    {
        $this->validateCleaner($request);

        $profilePicData = $request->hasFile('profile_pic') ? file_get_contents($request->file('profile_pic')->getRealPath()) : null;

        DB::transaction(function () use ($request, $profilePicData) {
            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'phone_no' => $request->phone_no,
                'password' => bcrypt($request->password),
                'role' => 'cleaner',
            ]);

            Cleaner::create([
                'cleaner_username' => $user->username,
                'cleaner_name' => $user->name,
                'cleaner_phoneNo' => $user->phone_no,
                'profile_pic' => $profilePicData,
                'status' => $request->status,
                'user_id' => $user->id,
            ]);
        });

        return redirect()->route('admin.cleaners.index')->with('success', 'Cleaner created successfully!');
    }

    /**
     * Edit a cleaner.
     */
    public function editCleaner(Cleaner $cleaner)
    {
        return view('admin.cleaners.edit', compact('cleaner'));
    }

    /**
     * Update a cleaner.
     */
    public function updateCleaner(Request $request, Cleaner $cleaner)
    {
        $this->validateCleaner($request);

        $cleaner->update([
            'cleaner_username' => $request->username ?? $cleaner->cleaner_username,
            'cleaner_name' => $request->name ?? $cleaner->cleaner_name,
            'cleaner_phoneNo' => $request->phone_no ?? $cleaner->cleaner_phoneNo,
            'status' => $request->status ?? $cleaner->status,
        ]);

        if ($request->filled('password')) {
            $cleaner->update(['cleaner_password' => bcrypt($request->password)]);
        }

        if ($request->hasFile('profile_pic')) {
            $cleaner->update(['profile_pic' => file_get_contents($request->file('profile_pic')->getRealPath())]);
        }

        return redirect()->route('admin.cleaners.index')->with('success', 'Cleaner updated successfully!');
    }

    /**
     * Delete a cleaner.
     */
    public function destroyCleaner(Cleaner $cleaner)
    {
        $user = User::findOrFail($cleaner->user_id);
        $cleaner->delete();
        $user->delete();

        return redirect()->route('admin.cleaners.index')->with('success', 'Cleaner deleted successfully!');
    }

    /**
     * Validate cleaner data.
     */
    private function validateCleaner(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'name' => 'required|string|max:255',
            'phone_no' => 'required|string|max:20',
            'status' => 'required|in:Available,Unavailable',
            'password' => 'nullable|confirmed|min:8',
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    }
}
