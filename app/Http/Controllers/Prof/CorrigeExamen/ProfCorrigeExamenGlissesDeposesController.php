<?php

namespace App\Http\Controllers\Prof\CorrigeExamen;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use App\Models\GlisserDeposer;
use App\Traits\LoadsCommentairesExercice;
use App\Traits\ResolvesExamenEtudiant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProfCorrigeExamenGlissesDeposesController extends Controller
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

        $attempt = ExamAttempt::where('examen_id', $examenId)
            ->where('student_id', $etudiant->id)
            ->where('numero_tentative', 1)
            ->where('status','!=','en_cour')
            ->firstOrFail();
        
        $glisserDeposers = GlisserDeposer::where('examen_id', $examen->id)
            ->orderBy('ordre')
            ->with([
                'questions' => fn($q) => $q->orderBy('ordre'),
                'questions.zones',
                'questions.items' => function ($q) use ($attempt, $student) {
                    $q->with(['zone', 'reponses' => function ($rq) use ($attempt, $student) {
                        $rq->where('exam_attempt_id', $attempt->id)
                            ->where('student_id', $student->id)
                            ->with('zoneChoisie');
                    }]);
                },
            ])
            ->get();

        [$typeGlissesDeposer, $commentsGlissesDeposer] = $this->loadCommentairesType($examen, 'glisserdeposer', $attempt);

        return view('prof.student.planexamencorrige.glisserdeposers', compact(
            'slug',
            'examen',
            'student',
            'attempt',
            'glisserDeposers',
            'typeGlissesDeposer',
            'commentsGlissesDeposer',
        ));
    }
}
