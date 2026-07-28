<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Student\Concerns\HandlesExamenFlow;
use App\Models\ExamAttempt;
use App\Models\Examen;
use App\Models\Student;
use App\Models\Text;
use App\Models\TextReponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentExamenTextController extends Controller
{
    use HandlesExamenFlow;

    public function show(Request $request, Examen $examen, string $slug, Text $text)
    {
        $questions = $text->textQuestions()->orderBy('ordre')->get();

        if ($questions->isEmpty()) {
            abort(404, 'Aucune question disponible.');
        }

        $totalPoints = $questions->sum('points');

        // Filaharan'ity text ity ao amin'ny examen
        $tousLesText = Text::where('examen_id', $examen->id)
            ->orderBy('ordre')
            ->get();

        $index = $tousLesText->search(fn($t) => $t->id === $text->id);
        $index = $index === false ? 0 : $index;
        $total = $tousLesText->count();

        $studentId = Auth::id();
        $student = Student::where('user_id', $studentId)->firstOrFail();

        $attempt = ExamAttempt::where('examen_id', $examen->id)
            ->where('student_id', $student->id)
            ->where('status', 'en_cours')
            ->latest('id')
            ->firstOrFail();

        // Alaina ny valiny efa nosoratan'ny mpianatra teo aloha (raha nisy), mba ho voatahiry ny texte
        $reponsesExistantes = TextReponse::where('exam_attempt_id', $attempt->id)
            ->whereIn('text_question_id', $questions->pluck('id'))
            ->pluck('reponse_texte', 'text_question_id')
            ->toArray();

        return view('student.examen.text.show', compact(
            'examen', 'slug', 'text', 'questions', 'totalPoints', 'index', 'total', 'reponsesExistantes'
        ));
    }

    public function store(Request $request, Examen $examen, string $slug, Text $text)
    {
        $validated = $request->validate([
            'reponses'   => ['required', 'array'],
            'reponses.*' => ['nullable', 'string'],
        ]);

        $studentId = Auth::id();
        $student = Student::where('user_id', $studentId)->firstOrFail();

        $attempt = ExamAttempt::where('examen_id', $examen->id)
            ->where('student_id', $student->id)
            ->where('status', 'en_cours')
            ->latest('id')
            ->firstOrFail();

        $questions = $text->textQuestions()->get();

        foreach ($questions as $question) {
            $reponseTexte = $validated['reponses'][$question->id] ?? '';

            // Esory ny valiny taloha ho an'ity question sy attempt ity
            TextReponse::where('text_question_id', $question->id)
                ->where('student_id', $studentId)
                ->where('exam_attempt_id', $attempt->id)
                ->delete();

            TextReponse::create([
                'text_question_id' => $question->id,
                'exam_attempt_id'  => $attempt->id,
                'student_id'       => $studentId,
                'reponse_texte'    => $reponseTexte,
                'submitted_at'     => now(),
                'note_obtenue'     => null, 
                'commentaire_prof' => null,
            ]);
        }

        // Tsy mikajy score eto satria "correction manuelle" — averina any aoriana rehefa vita ny fanitsian'ny prof

        $textSuivant = Text::where('examen_id', $examen->id)
            ->where('ordre', '>', $text->ordre)
            ->orderBy('ordre')
            ->first();

        if ($textSuivant) {
            return redirect()->route('examen.text.show', [
                'examen' => $examen->id,
                'slug'   => $slug,
                'text'   => $textSuivant->id,
            ]);
        }

        return $this->passerAuTypeExerciceSuivant($examen, $slug, 'text');
    }
}
