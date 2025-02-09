<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Supervisor;
use App\Models\Complaint;
use App\Models\Cleaner;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use App\Service\SupabaseService;

class AdminController extends Controller
{
    protected $supabaseService;

    /**
     * Inject the SupabaseService so we can also fetch complaint data from Supabase.
     *
     * @param SupabaseService $supabaseService
     */
    public function __construct(SupabaseService $supabaseService)
    {
        $this->supabaseService = $supabaseService;
    }

    /**
     * Display the dashboard with statistics from both MySQL and Supabase.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        // --- MySQL Data ---
        $totalComplaints    = Complaint::count();
        $activeCleaners     = Cleaner::where('status', 'available')->count();
        $totalOfficers      = User::where('role', 'officer')->count();
        $totalSupervisors   = User::where('role', 'supervisor')->count();

        $recentComplaints   = Complaint::select('id', 'comp_date', 'comp_time', 'comp_desc', 'comp_location', 'comp_status')
            ->latest()
            ->take(5)
            ->get();

        $monthlyComplaints  = Complaint::selectRaw("MONTH(comp_date) as month, COUNT(*) as count")
            ->groupBy('month')
            ->pluck('count', 'month');

        $cleanersByStatus   = Cleaner::selectRaw("status, COUNT(*) as count")
            ->groupBy('status')
            ->pluck('count', 'status');

        $complaintsByStatus = Complaint::selectRaw("comp_status, COUNT(*) as count")
            ->groupBy('comp_status')
            ->pluck('count', 'comp_status');

        // --- Supabase Data ---
        // Attempt to fetch complaints from Supabase. This assumes that your SupabaseService
        // has a getComplaints() method that returns an array of complaint records.
        try {
            $supabaseComplaints      = $this->supabaseService->getComplaints();
            $totalComplaintsSupabase = count($supabaseComplaints);
        } catch (\Exception $e) {
            // In case of error, you might log the error and set a default value.
            $totalComplaintsSupabase = 'Error: ' . $e->getMessage();
        }

        return view('admin.dashboard', compact(
            'totalComplaints',
            'activeCleaners',
            'totalOfficers',
            'totalSupervisors',
            'recentComplaints',
            'monthlyComplaints',
            'cleanersByStatus',
            'complaintsByStatus',
            'totalComplaintsSupabase'
        ));
    }

    /**
     * Show the admin profile edit form.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function editProfile(Request $request)
    {
        $user = $request->user();
        return view('admin.profile.edit', compact('user'));
    }

    /**
     * Update the admin profile.
     *
     * @param ProfileUpdateRequest $request
     * @return RedirectResponse
     */
    public function updateProfile(ProfileUpdateRequest $request)
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Redirect back to the admin profile edit page
        return Redirect::route('admin.profile.edit')->with('success', 'Profile updated successfully.');
    }

    /**
     * Delete the admin profile.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroyProfile(Request $request)
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Validate cleaner data.
     *
     * @param Request $request
     */
    private function validateCleaner(Request $request)
    {
        $request->validate([
            'username'    => 'required|string|max:255|unique:users,username',
            'name'        => 'required|string|max:255',
            'phone_no'    => 'required|string|max:20',
            'status'      => 'required|in:Available,Unavailable',
            'password'    => 'nullable|confirmed|min:8',
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    }
}
