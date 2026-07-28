<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfDashboardController extends Controller
{
    public function index()
    {
        return view('prof.dashboard');
    }
}
