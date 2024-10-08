<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AdminHistoryController extends Controller
{
    public function index()
    {
        return view('admin.history');
    }
}
