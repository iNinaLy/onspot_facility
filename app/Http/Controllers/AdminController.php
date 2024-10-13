<?php

namespace App\Http\Controllers;

use App\Models\Supervisor;
use App\Models\Complaint;
use App\Models\Cleaner;
use App\Models\Officer;
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
        $complaints = Complaint::all();
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

    public function officers()
    {
        $officers = Officer::all();
        return view('admin.officers.index', compact('officers'));
    }

    public function createOfficer()
    {
        return view('admin.officers.create');
    }

    public function storeOfficer(Request $request)
    {
        $this->validateOfficer($request);
        Officer::create($request->all());
        return redirect()->route('admin.officers')->with('success', 'Officer created successfully!');
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
        $this->validateOfficer($request);
        $officer->update($request->all());
        return redirect()->route('admin.officers')->with('success', 'Officer updated successfully!');
    }

    public function destroyOfficer(Officer $officer)
    {
        $officer->delete();
        return redirect()->route('admin.officers')->with('success', 'Officer deleted successfully!');
    }

    private function validateOfficer(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|string',
        ]);
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
