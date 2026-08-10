<?php

namespace App\Http\Controllers\Prof\CorrigeExamen;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use App\Models\Relier;
use App\Traits\LoadsCommentairesExercice;
use App\Traits\ResolvesExamenEtudiant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProfCorrigeExamenRelierController extends Controller
{
    use ResolvesExamenEtudiant, LoadsCommentairesExercice;

    public function showtache(string $slug, string $examenId, string $studentId)
    {
        //******** */
        $result = $this->resolveExamenEtudiant($slug, $examenId, $studentId);
        if ($result instanceof RedirectResponse) {
            return $result;
        }
        [$examen, $student, $etudiant] = $result;
        //******** */
        
        $reliers = Relier::where('examen_id', $examen->id)
            ->orderBy('ordre')
            ->with([
                'relierQuestions' => fn($q) => $q->orderBy('ordre'),
                'relierQuestions.paires',
            ])
            ->get();

        $attempt = ExamAttempt::where('examen_id', $examenId)
                    ->where('student_id', $etudiant->id)
                    ->where('numero_tentative', 1)
                    ->where('status','!=','en_cour')
                    ->firstOrFail();

        [$typeRelier, $commentsRelier] = $this->loadCommentairesType($examen, 'relier', $attempt);

        return view('prof.student.planexamencorrige.relier', compact(
            'slug',
            'examen',
            'student',
            'reliers',
            'attempt',
            'typeRelier',
            'commentsRelier'
        ));
    }
}
