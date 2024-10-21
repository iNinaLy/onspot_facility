<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Supervisor;
use App\Models\Complaint;
use App\Models\Cleaner;
use Illuminate\Http\Request;

class SupervisorController extends Controller
{
    /**
     * Display the supervisor dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        // Retrieve total cleaners and available cleaners
        $totalCleaners = Cleaner::count();
        $availableCleaners = Cleaner::where('status', 'available')->count();
        $unavailableCleaners = Cleaner::where('status', 'unavailable')->count();

        // Fetch the total number of supervisors
        $totalSupervisors = Supervisor::count();

        // Pass the variables to the view
        return view('supervisor.dashboard', compact('totalCleaners', 'availableCleaners', 'unavailableCleaners', 'totalSupervisors'));
    }

    /**
     * Show the history of complaints.
     *
     * @return \Illuminate\View\View
     */
    public function history()
    {
        // Fetch complaints with status 'in progress' or 'completed', eager load cleaners
        $complaints = Complaint::with('cleaners')
            ->whereIn('comp_status', ['in progress', 'completed'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('supervisor.history', compact('complaints'));
    }

    /**
     * Show all cleaners, with optional search filtering.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function cleaners(Request $request)
    {
        // Fetch the total number of cleaners
        $totalCleaners = Cleaner::count();
        $availableCount = Cleaner::where('status', 'available')->count();
        $unavailableCount = Cleaner::where('status', 'unavailable')->count();

        // Fetch all cleaners with pagination, including optional search filtering
        $search = $request->input('search');
        $cleaners = Cleaner::when($search, function ($query, $search) {
                return $query->where('cleaner_name', 'LIKE', "%{$search}%");
            })
            ->paginate(10); // Change this number to control how many items per page

        return view('supervisor.cleaners.index', compact('totalCleaners', 'availableCount', 'unavailableCount', 'cleaners'));
    }


    /**
     * List all supervisors.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $supervisors = Supervisor::all();
        return response()->json(['success' => true, 'data' => $supervisors], 200);
    }

    /**
     * Store a newly created supervisor.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            's_email' => 'required|email|unique:supervisors',
            's_pass' => 'required|min:8',
            's_name' => 'required|string|max:255',
            's_phoneNo' => 'required|string|max:15',
        ]);

        $supervisor = Supervisor::create([
            's_email' => $request->s_email,
            's_pass' => bcrypt($request->s_pass),
            's_name' => $request->s_name,
            's_phoneNo' => $request->s_phoneNo,
        ]);

        return response()->json(['success' => true, 'data' => $supervisor], 201);
    }

    /**
     * Display the specified supervisor.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $supervisor = Supervisor::find($id);

        if (!$supervisor) {
            return response()->json(['success' => false, 'message' => 'Supervisor not found'], 404);
        }

        // Fetch available cleaners
        $cleaners = Cleaner::where('status', 'available')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'supervisor' => $supervisor,
                'cleaners' => $cleaners // Include available cleaners in the response
            ]
        ], 200);
    }



    /**
     * Show the form for editing the specified supervisor.
     *
     * @param Supervisor $supervisor
     * @return \Illuminate\View\View
     */
    public function edit(Supervisor $supervisor)
    {
        return view('admin.supervisors.edit', compact('supervisor'));
    }

    /**
     * Update the specified supervisor.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            's_name' => 'required|string|max:255',
            's_email' => 'required|email|max:255|unique:supervisors,s_email,' . $id,
            's_phoneNo' => 'required|string|max:15',
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'officer_pass' => 'nullable|string|min:6|confirmed',
        ]);

        $supervisor = Supervisor::findOrFail($id);
        $supervisor->s_name = $request->s_name;
        $supervisor->s_email = $request->s_email;
        $supervisor->s_phoneNo = $request->s_phoneNo;

        if ($request->hasFile('profile_pic')) {
            $path = $request->file('profile_pic')->store('profile_pics', 'public');
            $supervisor->profile_pic = $path;
        }

        if ($request->filled('officer_pass')) {
            $supervisor->s_pass = bcrypt($request->officer_pass);
        }

        $supervisor->save();

        return redirect()->route('admin.supervisors.index')->with('success', 'Supervisor information updated successfully.');
    }

    /**
     * Remove the specified supervisor.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $supervisor = Supervisor::find($id);

        if (!$supervisor) {
            return response()->json(['success' => false, 'message' => 'Supervisor not found'], 404);
        }

        $supervisor->delete();

        return response()->json(['success' => true, 'message' => 'Supervisor deleted successfully'], 200);
    }
}
