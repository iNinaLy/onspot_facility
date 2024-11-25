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
        // Determine the active tab from the query parameter; default to 'today'
        $activeTab = $request->query('tab', 'today');

        // Fetch the current supervisor's ID (assuming authentication is set up)
        $supervisorId = Auth::id();

        // Base query for completed complaints assigned by the supervisor
        $complaintQuery = Complaint::where('assigned_by', $supervisorId)
            ->where('comp_status', 'completed')
            ->with([
                'cleaners:id,cleaner_name,cleaner_phoneNo',
                'officer:id,name'
            ])
            ->orderBy('comp_date', 'desc');

        // Fetch Complaints Based on Active Tab
        if ($activeTab === 'today') {
            $complaints = $complaintQuery->whereDate('comp_date', Carbon::today())->get();
        } elseif ($activeTab === 'this_week') {
            $complaints = $complaintQuery->whereBetween('comp_date', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])->whereDate('comp_date', '<>', Carbon::today())->get();
        } else { // 'older'
            $complaints = $complaintQuery->whereDate('comp_date', '<', Carbon::now()->startOfWeek())
                                         ->paginate(5, ['*'], 'older_page');
        }

        // Fetch Ongoing Complaints with Pagination
        $ongoingComplaints = Complaint::where('assigned_by', $supervisorId)
            ->where('comp_status', 'ongoing')
            ->with([
                'cleaners:id,cleaner_name,cleaner_phoneNo',
                'officer:id,name'
            ])
            ->orderBy('comp_date', 'desc')
            ->paginate(5, ['*'], 'ongoing_page');

        return view('supervisor.history', compact(
            'activeTab',
            'complaints',
            'ongoingComplaints'
        ));
    }
}
