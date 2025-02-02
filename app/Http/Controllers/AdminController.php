<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalAdmins = User::role('admin')->count();

        return view('admin.dashboard', compact('totalUsers', 'totalAdmins'));
    }
}
