<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    /**
     * Display the complaint history.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {

        $supervisorId = Auth::id();

        // Common base query to reduce redundancy
        $baseQuery = Complaint::where('assigned_by', $supervisorId)
            ->with(['cleaners:id,cleaner_name,cleaner_phoneNo', 'officer:id,name']);

        // Fetch ongoing complaints assigned by the supervisor
        $ongoingComplaints = (clone $baseQuery)
            ->where('comp_status', 'ongoing')
            ->orderBy('comp_date', 'desc')
            ->get();

        // Fetch today's completed complaints assigned by the supervisor
        $todaysComplaints = (clone $baseQuery)
            ->whereDate('comp_date', Carbon::today())
            ->where('comp_status', 'completed')
            ->orderBy('comp_date', 'desc')
            ->get();

        // Fetch this week's completed complaints assigned by the supervisor (excluding today)
        $thisWeeksComplaints = (clone $baseQuery)
            ->whereBetween('comp_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->where('comp_status', 'completed')
            ->whereDate('comp_date', '<>', Carbon::today())
            ->orderBy('comp_date', 'desc')
            ->get();

        // Fetch older completed complaints assigned by the supervisor with pagination
        $olderComplaints = (clone $baseQuery)
            ->whereDate('comp_date', '<', Carbon::now()->startOfWeek())
            ->where('comp_status', 'completed')
            ->orderBy('comp_date', 'desc')
            ->paginate(5); // Adjust the number per page as needed

        // Pass all the data to the view without duplication
        return view('supervisor.history', compact(
            'ongoingComplaints',
            'todaysComplaints',
            'thisWeeksComplaints',
            'olderComplaints'
        ));
    }
}
