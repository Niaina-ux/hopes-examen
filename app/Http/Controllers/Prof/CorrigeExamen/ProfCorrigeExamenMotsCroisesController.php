<?php

namespace App\Http\Controllers\Prof\CorrigeExamen;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use App\Models\MotsCroises;
use App\Traits\LoadsCommentairesExercice;
use App\Traits\ResolvesExamenEtudiant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProfCorrigeExamenMotsCroisesController extends Controller
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
            ->where('status','!=','en_cour')
            ->where('numero_tentative', 1)
            ->firstOrFail();
        
        $motsCroisesListe = MotsCroises::where('examen_id', $examen->id)
            ->orderBy('ordre')
            ->with([
                'motsCroisesMots' => fn($q) => $q->orderBy('numero'),
                'motsCroisesMots.reponses' => function ($q) use ($attempt) {
                    $q->where('exam_attempt_id', $attempt->id);
                },
            ])
            ->get();

        [$typeMotsCroises, $commentsMotsCroises] = $this->loadCommentairesType($examen, 'motscroises', $attempt);

        return view('prof.student.planexamencorrige.motscroises', compact(
            'slug',
            'examen',
            'student',
            'motsCroisesListe',
            'attempt',
            'typeMotsCroises',
            'commentsMotsCroises'
        ));
    }
}
