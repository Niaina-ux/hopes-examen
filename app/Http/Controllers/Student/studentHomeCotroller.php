<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use Illuminate\Http\Request;

class studentHomeCotroller extends Controller
{
    public function index()
    {
        $categories = Categorie::all();
        return view('student/home', compact('categories'));
    }
}
