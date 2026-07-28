<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Student\Concerns\HandlesExamenFlow;
use App\Models\ExamAttempt;
use App\Models\Examen;
use App\Models\MotsCroises;
use App\Models\MotsCroisesReponse;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentExamenMotsCroisesController extends Controller
{
    use HandlesExamenFlow;

    public function show(Request $request, Examen $examen, string $slug, MotsCroises $motscroises)
    {
        $mots = $motscroises->motsCroisesMots()->orderBy('numero')->get();

        if ($mots->isEmpty()) {
            abort(404, 'Aucun mot disponible.');
        }

        // ✅ Mikajy ny dimension an'ny grille (largeur/hauteur) automatika
        $largeur = 0;
        $hauteur = 0;

        foreach ($mots as $mot) {
            if ($mot->direction === 'horizontal') {
                $largeur = max($largeur, $mot->position_x + strlen($mot->reponse));
                $hauteur = max($hauteur, $mot->position_y + 1);
            } else { // vertical
                $largeur = max($largeur, $mot->position_x + 1);
                $hauteur = max($hauteur, $mot->position_y + strlen($mot->reponse));
            }
        }

        // ✅ Mamorona ny "grille" (array 2D) ho an'ny fampisehoana:
        // grille[y][x] = ['lettre' => null, 'numero' => null, 'mots_ids' => []]
        $grille = [];
        for ($y = 0; $y < $hauteur; $y++) {
            for ($x = 0; $x < $largeur; $x++) {
                $grille[$y][$x] = [
                    'active'    => false,
                    'numero'    => null,
                    'lettre'    => null, // <-- ampio ity
                    'mots_ids'  => [],
                ];
            }
        }

        foreach ($mots as $mot) {
            $longueur = strlen($mot->reponse);
            $positionsVisibles = $mot->positions_lettres_visibles ?? [];

            for ($i = 0; $i < $longueur; $i++) {
                $x = $mot->direction === 'horizontal' ? $mot->position_x + $i : $mot->position_x;
                $y = $mot->direction === 'horizontal' ? $mot->position_y : $mot->position_y + $i;

                $grille[$y][$x]['active'] = true;
                $grille[$y][$x]['mots_ids'][] = $mot->id;

                if ($i === 0) {
                    $grille[$y][$x]['numero'] = $mot->numero;
                }

                // ✅ Raha io position ($i) io dia anisan'ny "positions_lettres_visibles" an'ity mot ity,
                // dia asio ilay lettre mivantana ao amin'ny grille
                if (in_array($i, $positionsVisibles)) {
                    $grille[$y][$x]['lettre'] = strtoupper($mot->reponse[$i]);
                }
            }
        }

        $studentId = Auth::id();
        $student = Student::where('user_id', $studentId)->firstOrFail();

        $attempt = ExamAttempt::where('examen_id', $examen->id)
            ->where('student_id', $student->id)
            ->where('status', 'en_cours')
            ->latest('id')
            ->firstOrFail();

        // Alaina ny valiny efa nosoratan'ny mpianatra teo aloha (raha nisy)
        $reponsesExistantes = MotsCroisesReponse::where('exam_attempt_id', $attempt->id)
            ->whereIn('mots_croises_mot_id', $mots->pluck('id'))
            ->pluck('reponse_donnee', 'mots_croises_mot_id')
            ->toArray();

        // Filaharan'ity mots_croise ity ao amin'ny examen
        $tousLesMotsCroises = MotsCroises::where('examen_id', $examen->id)
            ->orderBy('ordre')
            ->get();

        $index = $tousLesMotsCroises->search(fn($m) => $m->id === $motscroises->id);
        $index = $index === false ? 0 : $index;
        $total = $tousLesMotsCroises->count();
        return view('student.examen.motscroise.show', compact(
            'examen', 'slug', 'motscroises', 'mots', 'grille', 'largeur', 'hauteur',
            'reponsesExistantes', 'index', 'total'
        ));
    }

    public function store(Request $request, Examen $examen, string $slug, MotsCroises $motscroises)
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

        $mots = $motscroises->motsCroisesMots()->get();

        foreach ($mots as $mot) {
            $reponseDonnee = strtoupper(trim($validated['reponses'][$mot->id] ?? ''));
            $estCorrecte = $reponseDonnee !== '' && $reponseDonnee === strtoupper($mot->reponse);

            MotsCroisesReponse::where('mots_croises_mot_id', $mot->id)
                ->where('student_id', $studentId)
                ->where('exam_attempt_id', $attempt->id)
                ->delete();

            MotsCroisesReponse::create([
                'mots_croises_mot_id' => $mot->id,
                'exam_attempt_id'     => $attempt->id,
                'student_id'          => $studentId,
                'reponse_donnee'      => $reponseDonnee,
                'est_correcte'        => $estCorrecte,
                'points_obtenus'      => $estCorrecte ? $mot->points : 0,
            ]);
        }

        $attempt->recalculerScore();

        $motsCroiseSuivant = MotsCroises::where('examen_id', $examen->id)
            ->where('ordre', '>', $motscroises->ordre)
            ->orderBy('ordre')
            ->first();

        if ($motsCroiseSuivant) {
            return redirect()->route('examen.motscroises.show', [
                'examen'     => $examen->id,
                'slug'       => $slug,
                'motsCroise' => $motsCroiseSuivant->id,
            ]);
        }

        return $this->passerAuTypeExerciceSuivant($examen, $slug, 'motscroises');
    }
}
