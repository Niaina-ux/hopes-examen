<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Code;
use App\Models\ExamAttempt;
use App\Models\Examen;
use App\Models\Fichier;
use App\Models\GlisserDeposer;
use App\Models\ImageExercice;
use App\Models\MotsCroises;
use App\Models\Pointiller;
use App\Models\Prof;
use App\Models\Qcm;
use App\Models\Redaction;
use App\Models\Relier;
use App\Models\Student;
use App\Models\StudentExamen;
use App\Models\Text;
use App\Models\User;
use App\Traits\CalculeStatistiquesExamen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfStudentController extends Controller
{
    use CalculeStatistiquesExamen;
    
    public function show(string $slug)
    {
        $categorie = Categorie::where('slug', $slug)->firstOrFail();

        $prof = Auth::user()->prof;
        $slugprof = $prof->categorie->slug;

        if ($categorie->id != $prof->categorie_id) {
            return redirect()->route('prof.student.show', $slugprof)
                ->with('error', 'horslug!');
        }

        $students = User::where('role', 'student')
            ->whereHas('student', function ($query) use ($categorie) {
                $query->where('categorie_id', $categorie->id);
            })
            ->with('student.categorie')
            ->paginate(10);
        
        return view('prof.student.show', compact('slug', 'categorie', 'students'));
    }

    public function studentstatexam(string $slug, string $student)
    {
        $categorie = Categorie::where('slug', $slug)->firstOrFail();
        $userStudent = Student::with('user')->find($student);
        $prof = Auth::user()->prof;
        $slugprof = $prof->categorie->slug;

        if (!$userStudent || $categorie->id != $prof->categorie_id) {
            return redirect()
                ->route('prof.student.show', $slugprof)
                ->with('error', 'Étudiant introuvable.');
        }

        $attempts = ExamAttempt::with('examen')
            ->where('student_id', $student)
            ->where('numero_tentative', 1)
            ->get();

        $statistiques = $attempts
            ->filter(fn($attempt) => $attempt->status === 'corrige')
            ->map(function ($attempt) {
                return [
                    'titre' => $attempt->examen->titre,
                    'date' => $attempt->date_fin?->format('d/m/Y'),
                    'pourcentage' => $this->calculerPourcentageAttempt($attempt->examen, $attempt),
                ];
            })
            ->filter(fn($s) => $s['pourcentage'] !== null)
            ->values();

        $moyenneGenerale = $statistiques->isNotEmpty()
            ? round($statistiques->avg('pourcentage'), 1)
            : null;

        $examen_planifie = StudentExamen::with('examen.categorie')
            ->where('user_id', $userStudent->id)
            ->where('termine', false)
            ->whereHas('examen', function ($query) {
                $query->where('status', '!=', 'brouillon');
            })
            ->orderBy('id', 'asc')
            ->get();

        return view('prof.student.examenallstudent', compact(
            'slug', 'userStudent', 'attempts','examen_planifie', 'statistiques', 'moyenneGenerale'
        ));
    }

}
