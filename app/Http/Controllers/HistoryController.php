<?php

namespace App\Http\Controllers;
use App\Models\Task;

use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index()
    {
        // Logic to retrieve and display the history of tasks
        // For example, you might fetch data from a database
        $taskHistory = Task::all(); // Replace this with your actual query logic
        return view('history.index', compact('taskHistory')); // Adjust the view as necessary
    }
    
}

