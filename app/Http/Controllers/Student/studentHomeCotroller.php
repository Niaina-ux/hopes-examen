<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class studentHomeCotroller extends Controller
{
    public function index()
    {
        
        if (Auth::check() && Auth::user()->role !== 'student') {
            return redirect()->route('login');
        }
        
        $categories = Categorie::withCount('examens')->get();

        $etudiantsApercu = User::where('role', 'student')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        $myCategorie = Student::where('user_id', Auth::id())->first()?->categorie;
        
        $totalEtudiants = User::where('role', 'student')->count();

        $totalTypesExamens = Categorie::count();

        return view('student.home', compact('categories', 'etudiantsApercu', 'totalEtudiants', 'totalTypesExamens', 'myCategorie'));
    }
}
