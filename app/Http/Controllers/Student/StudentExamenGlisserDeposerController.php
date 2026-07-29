<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Student\Concerns\HandlesExamenFlow;
use App\Models\ExamAttempt;
use App\Models\Examen;
use App\Models\GlisserDeposer;
use App\Models\GlisserDeposerItem;
use App\Models\GlisserDeposerQuestion;
use App\Models\GlisserDeposerReponse;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentExamenGlisserDeposerController extends Controller
{
    use HandlesExamenFlow;

    public function show(Request $request, Examen $examen, string $slug, GlisserDeposer $glisserdeposer)
    {
        $questions = $glisserdeposer->questions()->with(['zones', 'items'])->orderBy('ordre')->get();

        if ($questions->isEmpty()) {
            abort(404, 'Aucune question disponible.');
        }

        // ✅ Index an'ny question ankehitriny (araka ny query string ?q=0, default 0)
        $qIndex = (int) $request->query('q', 0);
        $qIndex = max(0, min($qIndex, $questions->count() - 1));

        $question = $questions[$qIndex];
        $totalQuestions = $questions->count();

        $studentId = Auth::id();
        $student = Student::where('user_id', $studentId)->firstOrFail();

        $attempt = ExamAttempt::where('examen_id', $examen->id)
            ->where('student_id', $student->id)
            ->where('status', 'en_cours')
            ->latest('id')
            ->firstOrFail();

        // Valiny efa nosoratana teo aloha ho an'ity question ity ihany
        $reponsesExistantes = GlisserDeposerReponse::where('exam_attempt_id', $attempt->id)
            ->whereIn('glisser_deposer_item_id', $question->items->pluck('id'))
            ->pluck('glisser_deposer_zone_id', 'glisser_deposer_item_id')
            ->toArray();

        // Filaharan'ity glisserDeposer ity ao amin'ny examen (progression manontolo)
        $tousLesGlisserDeposer = GlisserDeposer::where('examen_id', $examen->id)->orderBy('ordre')->get();
        $index = $tousLesGlisserDeposer->search(fn($g) => $g->id === $glisserdeposer->id);
        $index = $index === false ? 0 : $index;
        $total = $tousLesGlisserDeposer->count();

        return view('student.examen.glisserdeposer.show', compact(
            'examen', 'slug', 'glisserdeposer', 'question', 'qIndex', 'totalQuestions',
            'reponsesExistantes', 'index', 'total'
        ));
    }

    public function store(Request $request, Examen $examen, string $slug, GlisserDeposer $glisserdeposer) // ✅ mitovy case amin'ny route {glisserdeposer}
    {
        $validated = $request->validate([
            'question_id' => ['required', 'exists:glisser_deposer_questions,id'],
            'q_index'     => ['required', 'integer', 'min:0'],
            'reponses'    => ['nullable', 'array'], // ✅ nullable, mba tsy handà raha banga
        ]);

        $reponses = $validated['reponses'] ?? [];

        $studentId = Auth::id();
        $student = Student::where('user_id', $studentId)->firstOrFail();

        $attempt = ExamAttempt::where('examen_id', $examen->id)
            ->where('student_id', $student->id)
            ->where('status', 'en_cours')
            ->latest('id')
            ->firstOrFail();

        $question = GlisserDeposerQuestion::with('items')->findOrFail($validated['question_id']);

        foreach ($question->items as $item) {
            $zoneChoisieId = $reponses[$item->id] ?? null;
            $estCorrecte = $zoneChoisieId !== null && (int) $zoneChoisieId === $item->glisser_deposer_zone_id;

            GlisserDeposerReponse::where('glisser_deposer_item_id', $item->id)
                ->where('student_id', $studentId)
                ->where('exam_attempt_id', $attempt->id)
                ->delete();

            GlisserDeposerReponse::create([
                'glisser_deposer_item_id' => $item->id,
                'glisser_deposer_zone_id' => $zoneChoisieId,
                'exam_attempt_id'         => $attempt->id,
                'student_id'              => $studentId,
                'est_correcte'            => $estCorrecte,
                'points_obtenus' => $estCorrecte ? round($question->points / $question->items()->count(), 2) : 0,
            ]);
        }

        $attempt->recalculerScore();

        $totalQuestions = $glisserdeposer->questions()->count();
        $nextQIndex = $validated['q_index'] + 1;

        if ($nextQIndex < $totalQuestions) {
            return redirect()->route('examen.glisserdeposer.show', [
                'examen'         => $examen->id,
                'slug'           => $slug,
                'glisserdeposer' => $glisserdeposer->id,
                'q'              => $nextQIndex,
            ]);
        }

        $glisserDeposerSuivant = GlisserDeposer::where('examen_id', $examen->id)
            ->where('ordre', '>', $glisserdeposer->ordre ?? 0)
            ->orderBy('ordre')
            ->first();

        if ($glisserDeposerSuivant) {
            return redirect()->route('examen.glisserdeposer.show', [
                'examen'         => $examen->id,
                'slug'           => $slug,
                'glisserdeposer' => $glisserDeposerSuivant->id,
                'q'              => 0,
            ]);
        }

        return $this->passerAuTypeExerciceSuivant($examen, $slug, 'glisserdeposer');
    }
}
