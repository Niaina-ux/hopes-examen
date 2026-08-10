<?php

namespace App\Http\Controllers\Prof\CorrigeExamen;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use App\Models\Pointiller;
use App\Traits\LoadsCommentairesExercice;
use App\Traits\ResolvesExamenEtudiant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProfCorrigeExamenPointllerController extends Controller
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

        $pointillers = Pointiller::where('examen_id', $examen->id)
                    ->orderBy('ordre')
                    ->with([
                        'pointillerQuestions' => fn($q) => $q->orderBy('ordre'),
                        'pointillerQuestions.reponses',
                        'pointillerQuestions.reponses.etudiantReponses' => function ($q) use ($attempt) {
                            $q->where('exam_attempt_id', $attempt->id);
                        },
                    ])
                    ->get();

        [$typePointiller, $commentsPointiller] = $this->loadCommentairesType($examen, 'pointiller', $attempt);

        return view('prof.student.planexamencorrige.pointiller', compact(
            'slug',
            'examen',
            'student',
            'attempt',
            'pointillers',
            'typePointiller',
            'commentsPointiller'
        ));
    }
}
