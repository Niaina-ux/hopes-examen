<?php

namespace App\Http\Controllers\Prof\CorrigeExamen;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use App\Models\Examen;
use App\Models\Qcm;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;

class ProfCorrigeExamenQcmController extends Controller
{
    public function showtache(string $slug, Examen $examen, User $student)
    {
        return view('prof.student.planexamencorrige.qcm', compact(
            'slug',
            'examen',
            'student'
        ));
    }
}
