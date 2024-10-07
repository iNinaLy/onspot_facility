<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Task;
use Illuminate\Http\Request;

class AssignCleanerController extends Controller
{
    public function assignCleaners(Request $request, $complaintId)
    {
        // Validate the request
        $request->validate([
            'number_of_cleaners' => 'required|integer|min:1',
            'cleaner' => 'required|array',
            'cleaner.*' => 'exists:cleaners,cleaner_id'
        ]);

        // Retrieve the complaint and its associated task
        $complaint = Complaint::findOrFail($complaintId);
        $task = $complaint->task; // Assuming the complaint has a related task

        if (!$task) {
            // If no task exists, create a new one
            $task = Task::create([
                'comp_id' => $complaintId,
                'task_description' => $complaint->comp_desc,
                'status' => 'pending',
                'assigned_by' => auth('supervisor')->id(), // Correct authentication guard for supervisor
            ]);
        }

        // Assign selected cleaners to the task
        $cleaners = $request->input('cleaner');
        $task->cleaners()->sync($cleaners); // This will sync the cleaners to the task in the pivot table

        return redirect()->route('supervisor.complaints.index')
            ->with('success', 'Cleaners successfully assigned to the task.');
    }
}
