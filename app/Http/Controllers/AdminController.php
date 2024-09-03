<?php

namespace App\Http\Controllers;

use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalAdmins = User::role('admin')->count();

        return view('admin.dashboard', compact('totalUsers', 'totalAdmins'));
    }

}
