<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Student\Concerns\HandlesExamenFlow;
use App\Models\ExamAttempt;
use App\Models\Examen;
use App\Models\Pointiller;
use App\Models\PointillerEtudiantReponse;
use App\Models\Student;
use App\Models\TypeExercice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentExamenPointillerController extends Controller
{
    use HandlesExamenFlow;

    public function show(Request $request, Examen $examen, string $slug, Pointiller $pointiller)
    {
        $questions = $pointiller->pointillerQuestions()
            ->with('reponses.choices')
            ->orderBy('ordre')
            ->get();

        if ($questions->isEmpty()) {
            abort(404, 'Aucune question disponible.');
        }

        $totalPoints = $questions->sum('points');

        // ✅ Mikajy ny filaharan'ity pointiller ity ao amin'ny examen (index) sy ny totaly (total)
        $tousLesPointiller = Pointiller::where('examen_id', $examen->id)
            ->orderBy('ordre')
            ->get();

        $index = $tousLesPointiller->search(fn($p) => $p->id === $pointiller->id);
        $total = $tousLesPointiller->count();

        return view('student.examen.pointiller.show', compact(
            'examen', 'slug', 'pointiller', 'questions', 'totalPoints', 'index', 'total'
        ));
    }

    public function store(Request $request, Examen $examen, string $slug, Pointiller $pointiller)
    {
        $validated = $request->validate([
            'reponses' => ['required', 'array'],
        ]);

        $studentId = Auth::id();
        $student = Student::where('user_id', $studentId)->firstOrFail();

        // ✅ Alaina ny ExamAttempt "en_cours" ho an'ity examen sy student ity
        $attempt = ExamAttempt::where('examen_id', $examen->id)
            ->where('student_id', $student->id)
            ->where('status', 'en_cours')
            ->latest('id')
            ->firstOrFail();

        $questions = $pointiller->pointillerQuestions()->with('reponses')->get();

        foreach ($questions as $question) {
            foreach ($question->reponses as $reponse) {
                $reponseDonnee = $validated['reponses'][$reponse->id] ?? null;
                $estCorrecte = $reponseDonnee !== null
                    && trim($reponseDonnee) === trim($reponse->reponse_correcte);

                // ✅ Esory ny valiny taloha ho an'ity attempt ity ihany (tsy ny mpianatra manontolo)
                PointillerEtudiantReponse::where('pointiller_reponse_id', $reponse->id)
                    ->where('student_id', $studentId)
                    ->where('exam_attempt_id', $attempt->id)
                    ->delete();

                PointillerEtudiantReponse::create([
                    'pointiller_reponse_id' => $reponse->id,
                    'exam_attempt_id'       => $attempt->id, // ✅ ampio
                    'student_id'            => $studentId,
                    'reponse_donnee'        => $reponseDonnee,
                    'est_correcte'          => $estCorrecte,
                    'points_obtenus'        => $estCorrecte
                        ? round($question->points / $question->reponses->count(), 2)
                        : 0,
                ]);
            }
        }

        // ✅ Mikajy ny score an'ity pointiller ity, ary manampy azy amin'ny attempt->score
        $scorePointiller = PointillerEtudiantReponse::where('exam_attempt_id', $attempt->id)
            ->whereIn('pointiller_reponse_id', $questions->pluck('reponses')->flatten()->pluck('id'))
            ->sum('points_obtenus');


        // Pointiller manaraka ao anaty examen iray ihany (araka ny ordre)
        $pointillerSuivant = Pointiller::where('examen_id', $examen->id)
            ->where('ordre', '>', $pointiller->ordre)
            ->orderBy('ordre')
            ->first();

        if ($pointillerSuivant) {
            return redirect()->route('examen.pointiller.show', [
                'examen'     => $examen->id,
                'slug'       => $slug,
                'pointiller' => $pointillerSuivant->id,
            ]);
        }

        return $this->passerAuTypeExerciceSuivant($examen, $slug, 'pointiller');
    }

}
