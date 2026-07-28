<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Examen;
use App\Models\QcmWeb;
use App\Models\QcmWebReponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentExamenWebQcmController extends Controller
{
    public function show(Request $request, Examen $examen, QcmWeb $qcmWeb)
    {
        $questions = $qcmWeb->qcmWebQuestions()->with('qcmWebChoices')->orderBy('ordre')->get();
        
        if ($questions->isEmpty()) {
            abort(404, 'Aucune question disponible.');
        }

        // Index an'ny question ankehitriny (avy amin'ny query string ?q=0, default 0)
        $index = (int) $request->query('q', 0);
        $index = max(0, min($index, $questions->count() - 1)); // miaro raha diso ny index

        $question = $questions[$index];
        $total = $questions->count();

        return view('student.examen.webb.qcm.show', compact(
            'examen', 'qcmWeb', 'question', 'index', 'total'
        ));
    }


    public function answer(Request $request, Examen $examen, QcmWeb $qcmWeb)
    {
        $validated = $request->validate([
            'question_id' => ['required', 'exists:qcm_web_questions,id'],
            'choice_ids' => ['nullable', 'array'],
            'choice_ids.*' => ['exists:qcm_web_choices,id'],
        ]);

        //  dd($validated);

        $studentId = Auth::id();

        QcmWebReponse::where('qcm_web_question_id', $validated['question_id'])
            ->where('student_id', $studentId)
            ->delete();

        foreach ($validated['choice_ids'] ?? [] as $choiceId) {
            $choice = \App\Models\QcmWebChoice::find($choiceId);

            QcmWebReponse::create([
                'qcm_web_question_id' => $validated['question_id'],
                'qcm_web_choice_id' => $choiceId,
                'student_id' => $studentId,
                'est_correcte' => $choice->est_correcte,
            ]);
        }

        $index = (int) $request->query('q', 0);
        $questions = $qcmWeb->qcmWebQuestions()->orderBy('ordre')->get();

        if ($index + 1 < $questions->count()) {
            return redirect(route('student.examen.web.qcm', [$examen->id, $qcmWeb->id]) . '?q=' . ($index + 1));
        }

        $typeQcm = \App\Models\TypeExercice::where('slug', 'qcm')->firstOrFail();

        return redirect()->route('student.examen.next', [$examen->id, $typeQcm->id]);
    }
}
