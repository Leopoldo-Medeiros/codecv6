<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function showClients()
    {
        return view('admin.clients');
    }

    public function showCourses()
    {
        return view('admin.courses');
    }

    public function showPaths()
    {
        return view('admin.paths');
    }

    public function showSteps()
    {
        return view('admin.steps');
    }
}
