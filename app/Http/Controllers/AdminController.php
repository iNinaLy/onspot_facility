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
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        $totalComplaints = Complaint::count();
        $activeCleaners = Cleaner::where('status', 'available')->count();
        $totalOfficers = Officer::count();
        $recentComplaints = Complaint::latest()->take(5)->get();

        // Prepare data for charts
        $monthlyComplaints = Complaint::selectRaw("MONTH(comp_date) as month, COUNT(*) as count")
            ->groupBy('month')
            ->pluck('count', 'month');

        $cleanersByStatus = Cleaner::selectRaw("status, COUNT(*) as count")
            ->groupBy('status')
            ->pluck('count', 'status');

        // Complaint status counts for the donut chart
        $complaintsByStatus = Complaint::selectRaw("comp_status, COUNT(*) as count")
            ->groupBy('comp_status')
            ->pluck('count', 'comp_status');

        return view('admin.dashboard', compact(
            'totalComplaints', 'activeCleaners', 'totalOfficers', 'recentComplaints',
            'monthlyComplaints', 'cleanersByStatus', 'complaintsByStatus'
        ));
    }

    // Complaints Methods
   public function complaints()
    {
        // Fetch all complaints with pagination
        $complaints = Complaint::orderBy('comp_date', 'desc')->paginate(10); 
    
        // Pass complaints to the view
        return view('admin.complaints.index', compact('complaints'));
    }
    

    public function createComplaint()
    {
        return view('admin.complaints.create');
    }

    public function storeComplaint(Request $request)
    {
        $this->validateComplaint($request);
        Complaint::create($request->all());
        return redirect()->route('admin.complaints')->with('success', 'Complaint created successfully!');
    }

    public function showComplaint(Complaint $complaint)
    {
        return view('admin.complaints.show', compact('complaint'));
    }

    public function editComplaint(Complaint $complaint)
    {
        return view('admin.complaints.edit', compact('complaint'));
    }

    public function updateComplaint(Request $request, Complaint $complaint)
    {
        $this->validateComplaint($request);
        $complaint->update($request->all());
        return redirect()->route('admin.complaints')->with('success', 'Complaint updated successfully!');
    }

    public function destroyComplaint(Complaint $complaint)
    {
        $complaint->delete();
        return redirect()->route('admin.complaints')->with('success', 'Complaint deleted successfully!');
    }

    private function validateComplaint(Request $request)
    {
        $request->validate([
            'comp_desc' => 'required|string',
            'comp_status' => 'required|in:' . implode(',', Complaint::getStatuses()),
            'comp_location' => 'required|string',
            'comp_date' => 'required|date',
            'comp_time' => 'required',
        ]);
    }

    // Cleaners Methods
    public function cleaners()
    {
        $cleaners = Cleaner::all();
        return view('admin.cleaners.index', compact('cleaners'));
    }



    public function createCleaner()
    {
        return view('admin.cleaners.create');
    }

    public function storeCleaner(Request $request)
    {
        $this->validateCleaner($request);
        Cleaner::create($request->all());
        return redirect()->route('admin.cleaners')->with('success', 'Cleaner created successfully!');
    }

    public function showCleaner(Cleaner $cleaner)
    {
        return view('admin.cleaners.show', compact('cleaner'));
    }

    public function editCleaner(Cleaner $cleaner)
    {
        return view('admin.cleaners.edit', compact('cleaner'));
    }

    public function updateCleaner(Request $request, Cleaner $cleaner)
    {
        $this->validateCleaner($request);
        $cleaner->update($request->all());
        return redirect()->route('admin.cleaners')->with('success', 'Cleaner updated successfully!');
    }

    public function destroyCleaner(Cleaner $cleaner)
    {
        $cleaner->delete();
        return redirect()->route('admin.cleaners')->with('success', 'Cleaner deleted successfully!');
    }

    private function validateCleaner(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|string',
        ]);
    }

    // Officers Methods
    public function officers(Request $request)
    {
        $search = $request->query('search');

        // Filter officers by search query if provided
        $officers = Officer::when($search, function ($query, $search) {
            return $query->where('officer_name', 'LIKE', "%{$search}%");
        })->get();

        return view('admin.officers.index', compact('officers'));
    }

    public function createOfficer()
    {
        return view('admin.officers.create');
    }

    public function storeOfficer(Request $request)
    {
        // Validate input data
        $request->validate([
            'officer_name' => 'required|string|max:255',
            'officer_email' => 'required|email|unique:users,email',
            'officer_phoneNo' => 'required|string|max:20',
            'officer_pass' => 'required|string|min:8'
        ]);

        // Store the officer data in both users and officers tables
        DB::transaction(function () use ($request) {
            // Create the user record
            $user = User::create([
                'name' => $request->officer_name,
                'email' => $request->officer_email,
                'password' => Hash::make($request->officer_pass),
                'role' => 'officer',  // assuming you have a role column in users table
            ]);

            // Create the officer record linked to the user ID
            Officer::create([
                'user_id' => $user->id,
                'officer_name' => $request->officer_name,
                'officer_email' => $request->officer_email,
                'officer_phoneNo' => $request->officer_phoneNo,
            ]);
        });

        // Redirect back with a success message
        return redirect()->route('admin.officers')->with('success', 'Officer added successfully.');
    }

    public function search(Request $request)
    {
        $query = $request->get('query');

        // Search officers by name, email, or phone number
        $officers = Officer::where('officer_name', 'LIKE', "%{$query}%")
            ->orWhere('officer_email', 'LIKE', "%{$query}%")
            ->orWhere('officer_phoneNo', 'LIKE', "%{$query}%")
            ->get();

        return response()->json([
            'officers' => $officers
        ]);
    }

    public function showOfficer(Officer $officer)
    {
        return view('admin.officers.show', compact('officer'));
    }

    public function editOfficer(Officer $officer)
    {
        return view('admin.officers.edit', compact('officer'));
    }

    public function updateOfficer(Request $request, Officer $officer)
    {
        // Validate input
        $request->validate([
            'officer_name' => 'required|string|max:255',
            'officer_email' => 'required|email|unique:users,email,' . $officer->user->id, // unique validation for email
            'officer_phoneNo' => 'required|string|max:20',
            'officer_pass' => 'nullable|string|min:8'
        ]);

        // Update officer data
        $officer->officer_name = $request->officer_name;
        $officer->officer_email = $request->officer_email;
        $officer->officer_phoneNo = $request->officer_phoneNo;

        // Update password only if a new one is provided
        if ($request->filled('officer_pass')) {
            $officer->user->password = Hash::make($request->officer_pass);
            $officer->user->save();
        }

        // Save updated officer data
        $officer->save();

        return redirect()->route('admin.officers')->with('success', 'Officer updated successfully.');
    }

    public function destroyOfficer(Officer $officer)
    {
        $officer->delete();
        return redirect()->route('admin.officers')->with('success', 'Officer deleted successfully!');
    }

    // Supervisors Methods
    public function supervisors()
    {
        $supervisors = Supervisor::all();
        return view('admin.supervisors.index', compact('supervisors'));
    }

    public function createSupervisor()
    {
        return view('admin.supervisors.create');
    }

    public function storeSupervisor(Request $request)
    {
        $this->validateSupervisor($request);
        Supervisor::create($request->all());
        return redirect()->route('admin.supervisors')->with('success', 'Supervisor created successfully!');
    }

    public function searchSupervisors(Request $request)
    {
        $query = $request->get('query');

        // Validate the query
        if (!$query) {
            return response()->json(['supervisors' => []]);
        }

        // Search supervisors by name, email, or phone number
        $supervisors = Supervisor::where('s_name', 'LIKE', "%{$query}%")
            ->orWhere('s_email', 'LIKE', "%{$query}%")
            ->orWhere('s_phoneNo', 'LIKE', "%{$query}%")
            ->get();

        return response()->json([
            'supervisors' => $supervisors
        ]);
    }

    public function showSupervisor(Supervisor $supervisor)
    {
        return view('admin.supervisors.show', compact('supervisor'));
    }

    public function editSupervisor(Supervisor $supervisor)
    {
        return view('admin.supervisors.edit', compact('supervisor'));
    }

    public function updateSupervisor(Request $request, Supervisor $supervisor)
    {
        $this->validateSupervisor($request);
        $supervisor->update($request->all());
        return redirect()->route('admin.supervisors')->with('success', 'Supervisor updated successfully!');
    }

    public function destroySupervisor(Supervisor $supervisor)
    {
        $supervisor->delete();
        return redirect()->route('admin.supervisors')->with('success', 'Supervisor deleted successfully!');
    }

    private function validateSupervisor(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
    }
}
