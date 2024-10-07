<?php


namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Complaint;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // List all tasks
    public function index()
    {
        // Retrieve all tasks from the database
        $tasks = Task::all();
        return response()->json($tasks, 200); // Return tasks with status 200 OK
    }

    // Store a new task
    public function store(Request $request)
    {
        // Validate incoming data
        $validatedData = $request->validate([
            'no_of_cleaner' => 'required|integer',
            'comp_id' => 'required|exists:complaints,comp_id',
            's_id' => 'required|exists:supervisors,s_id',
        ]);
    
        // Create a new task
        $task = Task::create($validatedData);
    
        // Return the newly created task with status 201 Created
        return response()->json($task, 201);
    }

    // Show a single task by ID
    public function show($id)
    {
        // Find the task by ID or return a 404 error if not found
        $task = Task::findOrFail($id);

        // Return the task with status 200 OK
        return response()->json($task, 200);
    }

    // Update an existing task
    public function update(Request $request, $id)
    {
        // Find the task by ID or return a 404 error if not found
        $task = Task::findOrFail($id);

        // Validate incoming request data
        $validatedData = $request->validate([
            'no_of_cleaner' => 'required|integer',
            'comp_id' => 'required|exists:complaints,comp_id',
            's_id' => 'required|exists:supervisors,s_id',
        ]);

        // Update the task with validated data
        $task->update($validatedData);

        // Return the updated task with status 200 OK
        return response()->json($task, 200);
    }

    // Delete a task by ID
    public function destroy($id)
    {
        // Find the task by ID or return a 404 error if not found
        $task = Task::findOrFail($id);

        // Delete the task
        $task->delete();

        // Return a 204 No Content status to indicate successful deletion
        return response()->json(null, 204);
    }
}