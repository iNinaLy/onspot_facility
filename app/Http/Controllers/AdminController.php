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
    public function dashboard()
    {
        $totalComplaints = Complaint::count();
        $activeCleaners = Cleaner::where('status', 'available')->count();
        $totalOfficers = User::where('role', 'officer')->count();
        $totalSupervisors = User::where('role', 'supervisor')->count();

        $recentComplaints = Complaint::select('id', 'comp_date', 'comp_time', 'comp_desc', 'comp_location', 'comp_status')
            ->latest()
            ->take(5)
            ->get();

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

    public function complaints(Request $request)
    {
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
        $this->validateComplaint($request);

        Complaint::create($request->all());

        return redirect()->route('admin.complaints')->with('success', 'Complaint created successfully!');
    }

    public function editComplaint($id)
    {
        $complaint = Complaint::findOrFail($id);
        return view('admin.complaints.edit', compact('complaint'));
    }

    public function updateComplaint(Request $request, $id)
    {
        $this->validateComplaint($request);

        $complaint = Complaint::findOrFail($id);
        $complaint->update($request->all());

        return redirect()->route('admin.complaints')->with('success', 'Complaint updated successfully!');
    }

    public function updateStatus(Request $request, $id)
    {
        $complaint = Complaint::findOrFail($id);
        $complaint->comp_status = $request->status;
        $complaint->save();

        return redirect()->route('admin.complaints')->with('success', 'Complaint status updated successfully.');
    }


    public function destroyComplaint($id)
    {
        $complaint = Complaint::findOrFail($id);
        $complaint->delete();

        return redirect()->route('admin.complaints')->with('success', 'Complaint deleted successfully!');
    }

    public function searchComplaints(Request $request)
    {
        $query = $request->input('query');

        $complaints = Complaint::where('comp_desc', 'like', "%{$query}%")
            ->orWhere('comp_location', 'like', "%{$query}%")
            ->orWhere('id', 'like', "%{$query}%")
            ->paginate(10);

        return view('admin.complaints.index', compact('complaints', 'query'));
    }

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

    public function cleaners(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status');

        $cleaners = Cleaner::when($search, function ($query, $search) {
                return $query->where('cleaner_name', 'LIKE', "%{$search}%")
                             ->orWhere('cleaner_phoneNo', 'LIKE', "%{$search}%")
                             ->orWhere('cleaner_username', 'LIKE', "%{$search}%");
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', strtolower($status));
            })
            ->paginate(10);

        return view('admin.cleaners.index', compact('cleaners', 'search', 'status'));
    }

    public function createCleaner()
    {
        return view('admin.cleaners.create');
    }

    public function storeCleaner(Request $request)
    {
        $this->validateCleaner($request);

        $profilePicData = null;
        if ($request->hasFile('profile_pic')) {
            $profilePicData = file_get_contents($request->file('profile_pic')->getRealPath());
        }

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
            $cleaner->update(['profile_pic' => file_get_contents($request->file('profile_pic')->getRealPath())]);
        }

        return redirect()->route('admin.cleaners')->with('success', 'Cleaner updated successfully!');
    }

    public function resetCleanerPassword(Request $request, $cleaner)
    {
        $request->validate([
            'new_password' => 'required|confirmed|min:8',
        ]);

        $cleaner = Cleaner::findOrFail($cleaner);
        $cleaner->password = Hash::make($request->new_password);
        $cleaner->save();

        return redirect()->route('cleaners.edit', $cleaner)->with('status', 'Password has been reset successfully.');
    }

    public function officers(Request $request)
    {
        $search = $request->query('search');

        $officers = User::where('role', 'officer')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('phone_no', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
            })
            ->paginate(10);

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
            $officer->update([
                'password' => Hash::make($request->password),
            ]);
        }

        if ($request->hasFile('profile_pic')) {
            $path = $request->file('profile_pic')->store('profile_pics', 'public');
            $officer->update(['profile_pic' => $path]);
        }

        return redirect()->route('admin.officers')->with('success', 'Officer updated successfully!');
    }

    public function resetOfficerPassword(Request $request, $id)
    {
        $request->validate([
            'new_password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
            ],
        ]);

        $officer = User::findOrFail($id);
        $officer->password = Hash::make($request->new_password);
        $officer->save();

        return back()->with('status', 'Password updated successfully.');
    }

    public function supervisors(Request $request)
    {
        $query = User::where('role', 'supervisor');

        // Apply search filter if provided
        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                ->orWhere('phone_no', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Paginate the results
        $supervisors = $query->paginate(10);

        // Return the view with supervisors data
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

    public function resetSupervisorPassword(Request $request, $id)
    {
        $request->validate([
            'new_password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[a-z]/', // at least one lowercase letter
                'regex:/[A-Z]/', // at least one uppercase letter
                'regex:/[0-9]/', // at least one digit
                'regex:/[@$!%*#?&]/' // at least one special character
            ],
        ]);

        $supervisor = User::findOrFail($id); // Ensure the user exists in the `users` table
        $supervisor->password = Hash::make($request->new_password); // Hash the new password
        $supervisor->save(); // Save the updated password to the database

        return redirect()->route('admin.supervisors.index', $id)->with('status', 'Password has been reset successfully.');
    }



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
