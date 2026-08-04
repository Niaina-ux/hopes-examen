<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\ExamAttempt;
use App\Models\Examen;
use App\Models\Student;
use App\Models\StudentExamen;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfExamenStudentController extends Controller
{
    public function studentswithexamen(Request $request, string $slug, Examen $examen)
    {
        $tousLesStudentExamen = StudentExamen::where('examen_id', $examen->id)
            ->whereNotNull('date_examen')
            ->get();

        $datesDisponibles = $tousLesStudentExamen
            ->map(fn($se) => $se->date_examen->format('Y-m-d'))
            ->unique()
            ->sort()
            ->values();

        //  Manisa ny mpianatra isaky ny daty
        $nombreParDate = $tousLesStudentExamen
            ->groupBy(fn($se) => $se->date_examen->format('Y-m-d'))
            ->map(fn($group) => $group->count());

        $dateSelectionnee = $request->query('date', $datesDisponibles->last());

        $studentwithexam = StudentExamen::where('examen_id', $examen->id)
            ->when($dateSelectionnee, function ($query) use ($dateSelectionnee) {
                $query->whereDate('date_examen', $dateSelectionnee);
            })
            ->with('user.student')
            ->get();

        $userIds = $studentwithexam->pluck('user_id');
        $students = Student::whereIn('user_id', $userIds)->pluck('id', 'user_id');

        $attempts = ExamAttempt::where('examen_id', $examen->id)
            ->where('numero_tentative', 1)
            ->whereIn('student_id', $students->values())
            ->get()
            ->keyBy('student_id');

        return view('prof.student.studentswithexamen', compact(
            'slug', 'examen', 'studentwithexam', 'datesDisponibles', 'dateSelectionnee',
            'students', 'attempts', 'nombreParDate'
        ));
    }

    public function examenwherestudent(string $slug, Examen $examen, int $student_id)
    {
        $user = Auth::user();
        $prof = $user->prof;

        if (!$prof) {
            abort(403, 'Accès réservé aux professeurs.');
        }

        $profCategorie = Categorie::findOrFail($prof->categorie_id);

        $student = User::findOrFail($student_id);
        $etudiant = $student->student;

        if (!$etudiant) {
            return redirect()
                ->route('prof.page.notfound')
                ->with('error', 'Profil étudiant introuvable.');
        }

        if ($slug !== $profCategorie->slug) {
            return redirect()
                ->route('prof.page.notfound')
                ->with('error', 'Vous n\'êtes pas autorisé à consulter cette catégorie.');
        }

        if ($examen->categorie->slug !== $slug) {
            return redirect()
                ->route('prof.examen.studentswithexamen', [$slug, $examen->id])
                ->with('error', 'Examen introuvable pour cette catégorie.');
        }

        if ($etudiant->categorie_id !== $examen->categorie_id) {
            return redirect()
                ->route('prof.examen.studentswithexamen', [$slug, $examen->id])
                ->with('error', 'Cet étudiant n\'appartient pas à la catégorie de cet examen.');
        }

        $estAssigne = StudentExamen::where('examen_id', $examen->id)
            ->where('user_id', $student->id)
            ->exists();

        if (!$estAssigne) {
            return redirect()
                ->route('prof.examen.studentswithexamen', [$slug, $examen->id])
                ->with('error', 'Cet étudiant n\'est pas assigné à cet examen.');
        }

        $examen->load('typesExercice');
        $premierType = $examen->typesExercice->sortByDesc('ordre')->first();

        $attempt = ExamAttempt::where('examen_id', $examen->id)
            ->where('student_id', $etudiant->id)
            ->first();

        return view('prof.student.examenwherestudent', compact(
            'slug', 'examen', 'student', 'premierType', 'attempt', 'etudiant'
        ));
    }
}
