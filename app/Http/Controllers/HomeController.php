<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Return the view for the home page
        return view('supervisor.dashboard'); // Make sure this view file exists in resources/views/home.blade.php
    }
}
