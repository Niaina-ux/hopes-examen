<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;

class ProfStudentController extends Controller
{
    public function show(string $slug)
    {
        $categorie = Categorie::where('slug', $slug)->firstOrFail();

        $students = User::where('role', 'student')
            ->whereHas('student', function ($query) use ($categorie) {
                $query->where('categorie_id', $categorie->id);
            })
            ->with('student.categorie')
            ->paginate(10);
        
        return view('prof.student.show', compact('categorie', 'students'));
    }
}
