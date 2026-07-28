<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminStudentExameQcmController extends Controller
{
    public function index()
    {
        return view('admin.student.examen-qcm');
    }
}
