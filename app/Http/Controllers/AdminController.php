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

class AdminController extends Controller
{
    /**
     * Show the dashboard.
     */
    public function dashboard()
    {
        $totalComplaints = Complaint::count();
        $activeCleaners = Cleaner::where('status', 'available')->count();
        $totalOfficers = User::where('role', 'officer')->count();
        $totalSupervisors = User::where('role', 'supervisor')->count(); // Fetch total supervisors
        $recentComplaints = Complaint::select('id', 'comp_date', 'comp_time', 'comp_desc', 'comp_location', 'comp_status')
            ->latest()
            ->take(5)
            ->get();

        // Prepare data for charts
        $monthlyComplaints = Complaint::selectRaw("MONTH(comp_date) as month, COUNT(*) as count")
            ->groupBy('month')
            ->pluck('count', 'month');

        $cleanersByStatus = Cleaner::selectRaw("status, COUNT(*) as count")
            ->groupBy('status')
            ->pluck('count', 'status');

        $complaintsByStatus = Complaint::selectRaw("comp_status, COUNT(*) as count")
            ->groupBy('comp_status')
            ->pluck('count', 'comp_status');

        return view('admin.dashboard', compact(
            'totalComplaints', 'activeCleaners', 'totalOfficers', 'totalSupervisors', 'recentComplaints',
            'monthlyComplaints', 'cleanersByStatus', 'complaintsByStatus'
        ));
    }

    /**
 * Complaints Methods
 */
    public function complaints(Request $request)
    {
        // Handle sorting by status if provided, default sorting by date (descending)
        $status = $request->input('status');
        $query = Complaint::query();

        if ($status) {
            $query->where('comp_status', $status);
        }

        $complaints = $query->orderBy('comp_date', 'desc')->paginate(10);
        return view('admin.complaints.index', compact('complaints', 'status'));
    }


    public function createComplaint()
    {
        return view('admin.complaints.create');
    }

    public function storeComplaint(Request $request)
    {
        // Use the validateComplaint function to validate the incoming request
        $this->validateComplaint($request);

        // Create a new complaint from the validated data
        Complaint::create($request->all());

        // Redirect back to the complaints list with a success message
        return redirect()->route('admin.complaints')->with('success', 'Complaint created successfully!');
    }

    public function editComplaint($id)
    {
        $complaint = Complaint::findOrFail($id);  // Fetch the complaint using the ID from the route
        return view('admin.complaints.edit', compact('complaint'));  // Pass the complaint to the edit view
    }


    public function updateComplaint(Request $request, $id)
    {
        // Validate the incoming data
        $request->validate([
            'comp_status' => 'required|in:pending,on going,completed',
            'comp_location' => 'required|string',
            'comp_date' => 'required|date',
            'comp_time' => 'required'
        ]);

        // Find the complaint by ID
        $complaint = Complaint::findOrFail($id);

        // Update the complaint with the new data
        $complaint->update($request->all());

        // Redirect with a success message
        return redirect()->route('admin.complaints')->with('success', 'Complaint updated successfully!');
    }


    public function destroyComplaint($id)
    {
        $complaint = Complaint::findOrFail($id);  // Find the complaint by ID
        $complaint->delete();  // Delete the complaint

        return redirect()->route('admin.complaints')->with('success', 'Complaint deleted successfully!');
    }


    public function searchComplaints(Request $request)
    {
        // Handle the search query input
        $query = $request->input('query');
        
        // Perform the search based on description, location, or ID
        $complaints = Complaint::where('comp_desc', 'like', "%{$query}%")
            ->orWhere('comp_location', 'like', "%{$query}%")
            ->orWhere('id', 'like', "%{$query}%") // Use 'id' instead of 'comp_id' since Laravel uses 'id'
            ->paginate(10);

        // Pass the results to the index view
        return view('admin.complaints.index', compact('complaints', 'query'));
    }

    private function validateComplaint(Request $request)
    {
        // Validate the incoming complaint data
        $request->validate([
            'comp_desc' => 'required|string',
            'comp_status' => 'required|in:pending,on going,completed',
            'comp_location' => 'required|string',
            'comp_date' => 'required|date',
            'comp_time' => 'required|date_format:H:i', // Ensuring time is in a proper format
        ]);
    }

    /**
     * Cleaners Methods
     */
    public function cleaners(Request $request)
    {
        $search = $request->query('search');
        
        // Fetch data from the cleaners table, applying the search filter
        $cleaners = Cleaner::when($search, function ($query, $search) {
                return $query->where('cleaner_name', 'LIKE', "%{$search}%")
                            ->orWhere('cleaner_phoneNo', 'LIKE', "%{$search}%")
                            ->orWhere('cleaner_username', 'LIKE', "%{$search}%");
            })
            ->paginate(10); // Adjust the pagination limit as necessary

        return view('admin.cleaners.index', compact('cleaners'));
    }

    public function createCleaner()
    {
        return view('admin.cleaners.create');
    }

    public function storeCleaner(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users,username',
            'name' => 'required',
            'phone_no' => 'required',
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required',
            'password' => 'required|confirmed|min:6',
        ]);

        // Handle profile picture as binary data (BLOB)
        $profilePicData = null;
        if ($request->hasFile('profile_pic')) {
            $profilePicData = file_get_contents($request->file('profile_pic')->getRealPath());
        }

        // Store the cleaner in the users table
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'phone_no' => $request->phone_no,
            'password' => bcrypt($request->password),
            'role' => 'cleaner', // Ensure the role is set to cleaner
        ]);

        // Store the cleaner in the cleaners table
        Cleaner::create([
            'cleaner_username' => $user->username,
            'cleaner_name' => $user->name,
            'cleaner_phoneNo' => $user->phone_no,
            'profile_pic' => $profilePicData,
            'status' => $request->status,
            'user_id' => $user->id, // Store the user id
        ]);

        return redirect()->route('admin.cleaners.index')->with('success', 'Cleaner created successfully!');
    }


    public function editCleaner(Cleaner $cleaner)
    {
        return view('admin.cleaners.edit', compact('cleaner'));
    }

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
            $path = $request->file('profile_pic')->store('profile_pics', 'public');
            $cleaner->update(['profile_pic' => $path]);
        }

        return redirect()->route('admin.cleaners')->with('success', 'Cleaner updated successfully!');
    }

    public function destroyCleaner($id)
    {
        $cleaner = Cleaner::findOrFail($id);
        $user = User::findOrFail($cleaner->user_id);
    
        // Delete cleaner and associated user
        $cleaner->delete();
        $user->delete();
    
        return redirect()->route('admin.cleaners.index')->with('success', 'Cleaner deleted successfully!');
    }
    

    private function validateCleaner(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'phone_no' => 'required|string|max:20',
            'status' => 'required|string|in:Available,Unavailable',
            'password' => 'nullable|confirmed|min:8',
        ]);
    }

    /**
     * Officers Methods
     */
    public function officers(Request $request)
    {
        $search = $request->query('search');
        $officers = User::where('role', 'officer')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('username', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
            })
            ->get();

        return view('admin.officers.index', compact('officers'));
    }

    public function createOfficer()
    {
        return view('admin.officers.create');
    }

    public function storeOfficer(Request $request)
    {
        $request->validate([
            'officer_name' => 'required|string|max:255',
            'officer_email' => 'required|email|unique:users,email',
            'officer_phoneNo' => 'required|string|max:20',
            'officer_pass' => 'required|string|min:8',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->officer_name,
                'email' => $request->officer_email,
                'password' => Hash::make($request->officer_pass),
                'role' => 'officer',
            ]);

            Officer::create([
                'user_id' => $user->id,
                'officer_name' => $request->officer_name,
                'officer_email' => $request->officer_email,
                'officer_phoneNo' => $request->officer_phoneNo,
            ]);
        });

        return redirect()->route('admin.officers')->with('success', 'Officer added successfully.');
    }

    public function editOfficer($id)
    {
        $officer = User::where('id', $id)->where('role', 'officer')->firstOrFail();
        return view('admin.officers.edit', compact('officer'));
    }

    public function updateOfficer(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone_no' => 'required|string|max:20',
            'password' => 'nullable|string|min:8',
            'profile_pic' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $officer = User::where('id', $id)->where('role', 'officer')->firstOrFail();

        $officer->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone_no' => $request->phone_no,
        ]);

        if ($request->filled('password')) {
            $officer->update(['password' => Hash::make($request->password)]);
        }

        if ($request->hasFile('profile_pic')) {
            $path = $request->file('profile_pic')->store('profile_pics', 'public');
            $officer->update(['profile_pic' => $path]);
        }

        return redirect()->route('admin.officers')->with('success', 'Officer updated successfully!');
    }

    public function destroyOfficer(User $officer)
    {
        $officer->delete();
        return redirect()->route('admin.officers')->with('success', 'Officer deleted successfully!');
    }

    public function supervisors(Request $request)
    {
        $search = $request->query('search');
        $supervisors = User::where('role', 'supervisor')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('username', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
            })
            ->paginate(10); // Pagination: 10 items per page

        return view('admin.supervisors.index', compact('supervisors'));
    }

    public function createSupervisor()
    {
        return view('admin.supervisors.create');
    }

    public function storeSupervisor(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_no' => 'required|string|max:20',
            'profile_pic' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('profile_pic')) {
            $data['profile_pic'] = $request->file('profile_pic')->store('profile_pics', 'public');
        }

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone_no' => $data['phone_no'],
            'role' => 'supervisor',
            'profile_pic' => $data['profile_pic'] ?? null,
        ]);

        return redirect()->route('admin.supervisors.index')->with('success', 'Supervisor created successfully!');
    }

    public function editSupervisor($id)
    {
        $supervisor = User::where('id', $id)->where('role', 'supervisor')->firstOrFail();
        return view('admin.supervisors.edit', compact('supervisor'));
    }

    public function updateSupervisor(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone_no' => 'required|string|max:20',
            'profile_pic' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $supervisor = User::where('id', $id)->where('role', 'supervisor')->firstOrFail();

        $supervisor->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone_no' => $request->phone_no,
        ]);

        if ($request->hasFile('profile_pic')) {
            $path = $request->file('profile_pic')->store('profile_pics', 'public');
            $supervisor->update(['profile_pic' => $path]);
        }

        return redirect()->route('admin.supervisors.index')->with('success', 'Supervisor updated successfully!');
    }
    public function destroySupervisor($id)
    {
        $supervisor = User::where('id', $id)->where('role', 'supervisor')->firstOrFail();
        $supervisor->delete();
        return redirect()->route('admin.supervisors.index')->with('success', 'Supervisor deleted successfully!');
    }
    private function validateSupervisor(Request $request)
    {
        $request->validate([
            's_name' => 'required|string|max:255',
            's_email' => 'required|email|unique:supervisors,s_email,' . ($request->route('supervisor')->id ?? 'NULL'),
            's_phoneNo' => 'required|numeric',
            'profile_pic' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
    }
}
