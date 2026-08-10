<?php

namespace App\Http\Controllers\Prof\CorrigeExamen;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use App\Models\Examen;
use App\Models\Qcm;
use App\Models\Student;
use App\Models\User;
use App\Traits\LoadsCommentairesExercice;
use App\Traits\ResolvesExamenEtudiant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProfCorrigeExamenQcmController extends Controller
{
    use ResolvesExamenEtudiant, LoadsCommentairesExercice;

    public function showtache(string $slug, string $examenId, string $studentId)
    {
        $result = $this->resolveExamenEtudiant($slug, $examenId, $studentId);
        if ($result instanceof RedirectResponse) {
            return $result;
        }
        [$examen, $student, $etudiant] = $result;

        $attempt = ExamAttempt::where('examen_id', $examen->id)
            ->where('numero_tentative', 1)
            ->where('status','!=','en_cour')
            ->where('student_id', $etudiant->id)
            ->firstOrFail();

        $qcms = Qcm::where('examen_id', $examen->id)
            ->orderBy('ordre')
            ->with([
                'qcmQuestions' => fn($q) => $q->orderBy('ordre'),
                'qcmQuestions.qcmChoices',
                'qcmQuestions.qcmReponsesEtudiants' => function ($query) use ($attempt) {
                    $query->where('exam_attempt_id', $attempt->id)
                        ->with('qcmchoice');
                },
            ])
            ->get();

        [$typeQcm, $commentsQcm] = $this->loadCommentairesType($examen, 'qcm', $attempt);

        return view('prof.student.planexamencorrige.qcm', compact(
            'slug', 'examen', 'student', 'attempt', 'qcms', 'typeQcm', 'commentsQcm'
        ));
    }
}
