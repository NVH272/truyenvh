<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Trang Dashboard admin
     */
    public function index()
    {
        return view('admin.dashboard');
    }
}
