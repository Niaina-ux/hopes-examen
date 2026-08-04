<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Student\Concerns\HandlesExamenFlow;
use App\Models\ExamAttempt;
use App\Models\Examen;
use App\Models\Qcm;
use App\Models\QcmChoice;
use App\Models\QcmQuestion;
use App\Models\QcmReponse;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentExamenQcmController extends Controller
{
    use HandlesExamenFlow;

    public function show(Request $request, Examen $examen, string $slug, Qcm $qcm)
    {
        $questions = $qcm->qcmQuestions()
        ->with('qcmChoices')
        ->orderBy('ordre')
        ->get();

        if ($questions->isEmpty()) {
            abort(404, 'Aucune question disponible.');
        }

        $index = (int) $request->query('q', 0);
        $index = max(0, min($index, $questions->count() - 1));

        $question = $questions[$index];
        $total = $questions->count();
        $estDerniereQuestion = $index === $total - 1;

        return view('student.examen.qcm.show', compact(
            'examen', 'qcm', 'question', 'index', 'total', 'estDerniereQuestion'
        ));
    }


    public function answer(Request $request, Examen $examen, string $slug, Qcm $qcm)
    {
        $question = QcmQuestion::findOrFail($request->question_id);
        $isTimeout = $request->boolean('timeout');

        $rules = [
            'question_id' => 'required|exists:qcm_questions,id',
            'index'       => 'required|integer|min:0',
        ];

        if (!$isTimeout) {
            if ($question->reponse_type === 'multiple') {
                $rules['choice_ids'] = 'required|array|min:1';
                $rules['choice_ids.*'] = 'required|exists:qcm_choices,id';
            } else {
                $rules['choice_id'] = 'required|exists:qcm_choices,id';
            }
        }

        $validated = $request->validate($rules);

        $studentId = Auth::id();
        $student = Student::where('user_id', $studentId)->firstOrFail();

        $attempt = ExamAttempt::where('examen_id', $examen->id)
            ->where('student_id', $student->id)
            ->where('status', 'en_cours')
            ->latest('id')
            ->firstOrFail();

        QcmReponse::where([
            'student_id'      => $studentId,
            'qcm_question_id' => $question->id,
            'exam_attempt_id' => $attempt->id,
        ])->delete();

        if ($isTimeout) {
            QcmReponse::create([
                'qcm_question_id' => $question->id,
                'qcm_choice_id'   => null,
                'exam_attempt_id' => $attempt->id,
                'student_id'      => $studentId,
                'est_correcte'    => false,
                'points_obtenus'  => 0,
            ]);

        } elseif ($question->reponse_type === 'multiple') {

            $selectedIds = $validated['choice_ids'];
            $totalCorrect = $question->qcmChoices()->where('est_correcte', true)->count();

            if (count($selectedIds) > $totalCorrect) {
                return back()->withErrors([
                    'choice_ids' => "Vous ne pouvez sélectionner que {$totalCorrect} réponse(s) pour cette question.",
                ])->withInput();
            }

            $pointsParChoix = $totalCorrect > 0
                ? round($question->points / $totalCorrect, 2)
                : 0;

            foreach ($selectedIds as $choiceId) {
                $choice = QcmChoice::findOrFail($choiceId);
                $pointsObtenus = $choice->est_correcte ? $pointsParChoix : 0;

                QcmReponse::create([
                    'qcm_question_id' => $question->id,
                    'qcm_choice_id'   => $choice->id,
                    'exam_attempt_id' => $attempt->id,
                    'student_id'      => $studentId,
                    'est_correcte'    => $choice->est_correcte,
                    'points_obtenus'  => $pointsObtenus,
                ]);
            }

        } else {
            $choice = QcmChoice::findOrFail($validated['choice_id']);
            $pointsGagnes = $choice->est_correcte ? $question->points : 0;

            QcmReponse::create([
                'qcm_question_id' => $question->id,
                'qcm_choice_id'   => $choice->id,
                'exam_attempt_id' => $attempt->id,
                'student_id'      => $studentId,
                'est_correcte'    => $choice->est_correcte,
                'points_obtenus'  => $pointsGagnes,
            ]);
        }

        // Question suivante ao anatin'io Qcm io ihany
        $nextIndex = $validated['index'] + 1;
        $totalQuestions = $qcm->qcmQuestions()->count();

        if ($nextIndex < $totalQuestions) {
            return redirect()->route('examen.qcm.show', [
                'examen' => $examen->id,
                'slug'   => $slug,
                'qcm'    => $qcm->id,
                'q'      => $nextIndex,
            ]);
        }

        // Qcm suivant ao amin'io examen io ihany
        $qcmSuivant = Qcm::where('examen_id', $examen->id)
            ->where('ordre', '>', $qcm->ordre)
            ->orderBy('ordre')
            ->first();

        if ($qcmSuivant) {
            return redirect()->route('examen.qcm.show', [
                'examen' => $examen->id,
                'slug'   => $slug,
                'qcm'    => $qcmSuivant->id,
                'q'      => 0,
            ]);
        }

        // ✅ Vita ny Qcm rehetra — mijery raha misy type_exercice manaraka
        return $this->passerAuTypeExerciceSuivant($examen, $slug, 'qcm');
    }
}
