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
     * Write code on Method
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        // Retrieve the counts
        $totalCleaners = Cleaner::count();
        $availableCleaners = Cleaner::where('status', 'available')->count();

        // Pass the data to the view
        return view('Supervisor.dashboard', compact('totalCleaners', 'availableCleaners'));
    }

    public function history()
    {
        // Fetch complaints with status 'in progress' or 'completed', eager load cleaners
        $complaints = Complaint::with('cleaners')
            ->whereIn('comp_status', ['in progress', 'completed']) // Filter by the desired statuses
            ->orderBy('updated_at', 'desc') // Order by the latest updates
            ->get();

        return view('supervisor.history', compact('complaints'));
    }

    public function showComplaintsPage()
    {
        // Fetch the most recent complaint based on comp_date and comp_time fields
        $recentComplaint = Complaint::orderBy('comp_date', 'desc')
                                    ->orderBy('comp_time', 'desc')
                                    ->first();

        // Pass it to the view
        return view('supervisor.complaints.index', compact('recentComplaint'));
    }

    

}
    /* Display a listing of all supervisors
    public function index()
    {
        $supervisors = Supervisor::all();
        return response()->json(['success' => true, 'data' => $supervisors], 200);
    }

    // Store a newly created supervisor
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

    // Display the specified supervisor
    public function show($id)
    {
        $supervisor = Supervisor::find($id);

        if (!$supervisor) {
            return response()->json(['success' => false, 'message' => 'Supervisor not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $supervisor], 200);
    }

    // Update the specified supervisor
    public function update(Request $request, $id)
    {
        $supervisor = Supervisor::find($id);

        if (!$supervisor) {
            return response()->json(['success' => false, 'message' => 'Supervisor not found'], 404);
        }

        $request->validate([
            's_email' => 'required|email|unique:supervisors,s_email,' . $supervisor->s_id,
            's_name' => 'required|string|max:255',
            's_phoneNo' => 'required|string|max:15',
        ]);

        $supervisor->update($request->all());

        return response()->json(['success' => true, 'data' => $supervisor], 200);
    }

    // Remove the specified supervisor
    public function destroy($id)
    {
        $supervisor = Supervisor::find($id);

        if (!$supervisor) {
            return response()->json(['success' => false, 'message' => 'Supervisor not found'], 404);
        }

        $supervisor->delete();

        return response()->json(['success' => true, 'message' => 'Supervisor deleted successfully'], 200);
    }
}*/