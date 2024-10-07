<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Cleaner;

class TaskCleanerController extends Controller
{
    // List all cleaners assigned to a task
    public function showCleanersForTask($taskId)
    {
        $task = Task::findOrFail($taskId);
        $cleaners = $task->cleaners;
        return response()->json($cleaners, 200);
    }

    // Assign a cleaner to a task
    public function assignCleanerToTask(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,task_id',
            'cleaner_id' => 'required|exists:cleaners,cleaner_id',
        ]);

        $task = Task::findOrFail($request->task_id);
        $task->cleaners()->attach($request->cleaner_id);

        return response()->json(['message' => 'Cleaner assigned to task successfully'], 201);
    }

    // Remove a cleaner from a task
    public function removeCleanerFromTask(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,task_id',
            'cleaner_id' => 'required|exists:cleaners,cleaner_id',
        ]);

        $task = Task::findOrFail($request->task_id);
        $task->cleaners()->detach($request->cleaner_id);

        return response()->json(['message' => 'Cleaner removed from task successfully'], 200);
    }
}
