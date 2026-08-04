<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Student\Concerns\HandlesExamenFlow;
use App\Models\ExamAttempt;
use App\Models\Examen;
use App\Models\Relier;
use App\Models\RelierReponse;
use App\Models\Student;
use App\Models\TypeExercice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentExamenRelierController extends Controller
{
    use HandlesExamenFlow;

    public function show(Request $request, Examen $examen, string $slug, Relier $relier)
    {
        $questions = $relier->relierQuestions()
            ->with('paires')
            ->orderBy('ordre')
            ->get();

        if ($questions->isEmpty()) {
            abort(404, 'Aucune question disponible.');
        }

        $totalPoints = $questions->sum('points');

        // Mikajy ny filaharan'ity relier ity ao amin'ny examen (index/total)
        $tousLesRelier = Relier::where('examen_id', $examen->id)
            ->orderBy('ordre')
            ->get();

        $index = $tousLesRelier->search(fn($r) => $r->id === $relier->id);
        $index = $index === false ? 0 : $index;
        $total = $tousLesRelier->count();

        // Isaky ny question, manomana lisitry gauche/droite misaraka, samy mifangaro
        $questions->transform(function ($question) {
            $question->paires_gauche = $question->paires->sortBy('ordre_gauche')->values();
            $question->paires_droite = $question->paires->sortBy('ordre_droite')->values();
            return $question;
        });

        return view('student.examen.relier.show', compact(
            'examen', 'slug', 'relier', 'questions', 'totalPoints', 'index', 'total'
        ));
    }

    public function store(Request $request, Examen $examen, string $slug, Relier $relier)
    {
        $validated = $request->validate([
            'liaisons' => ['required', 'array'],
        ]);

        $studentId = Auth::id();
        $student = Student::where('user_id', $studentId)->firstOrFail();

        $attempt = ExamAttempt::where('examen_id', $examen->id)
            ->where('student_id', $student->id)
            ->where('status', 'en_cours')
            ->latest('id')
            ->firstOrFail();

        $questions = $relier->relierQuestions()->with('paires')->get();

        // Lisitry ny paires rehetra ao amin'ity relier ity, indexed by id
        $toutesLesPaires = $questions->pluck('paires')->flatten()->keyBy('id');

        foreach ($questions as $question) {
            foreach ($question->paires as $paire) {
                // Ny "gauche" no paire mihitsy; ny "droite" nofidin'ny mpianatra ho an'io gauche io
                $paireChoisieId = $validated['liaisons'][$paire->id] ?? null;

                // Marina raha ny paire nofidina ho "droite" dia mitovy amin'ilay paire mihitsy
                // (satria element_gauche sy element_droite marina dia ao anaty row iray ihany)
                $estCorrecte = $paireChoisieId !== null
                    && (int) $paireChoisieId === (int) $paire->id;

                // Esory ny valiny taloha ho an'ity paire sy attempt ity
                RelierReponse::where('relier_paire_id', $paire->id)
                    ->where('student_id', $studentId)
                    ->where('exam_attempt_id', $attempt->id)
                    ->delete();

                $pointsParPaire = $question->paires->count() > 0
                    ? round($question->points / $question->paires->count(), 2)
                    : 0;

                RelierReponse::create([
                    'relier_paire_id'    => $paire->id,
                    'exam_attempt_id'    => $attempt->id,
                    'student_id'         => $studentId,
                    'paire_choisie_id'   => $paireChoisieId,
                    'est_correcte'       => $estCorrecte,
                    'points_obtenus'     => $estCorrecte ? $pointsParPaire : 0,
                ]);
            }
        }


        // Relier suivant ao anaty examen iray ihany (araka ny ordre)
        $relierSuivant = Relier::where('examen_id', $examen->id)
            ->where('ordre', '>', $relier->ordre)
            ->orderBy('ordre')
            ->first();

        if ($relierSuivant) {
            return redirect()->route('examen.relier.show', [
                'examen' => $examen->id,
                'slug'   => $slug,
                'relier' => $relierSuivant->id,
            ]);
        }

        // Vita ny relier rehetra — mijery raha misy type_exercice manaraka
        return $this->passerAuTypeExerciceSuivant($examen, $slug, 'relier');
    }



}
