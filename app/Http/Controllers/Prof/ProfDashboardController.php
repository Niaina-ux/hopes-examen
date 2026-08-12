<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\ExamAttempt;
use App\Models\Examen;
use App\Models\Student;
use App\Traits\CalculeStatistiquesExamen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfDashboardController extends Controller
{
    use CalculeStatistiquesExamen;

    public function index(Request $request)
    {
        $prof = Auth::user()->prof;
        abort_if(!$prof, 403, 'Accès réservé aux professeurs.');

        $categorieId = $prof->categorie_id;
        $slug = Categorie::find($categorieId);
        $totalEtudiants = Student::where('categorie_id', $categorieId)->count();
        $totalExamens = Examen::where('categorie_id', $categorieId)->count();

        $anneeSelectionnee = (int) $request->input('annee', now()->year);

        $anneesDisponibles = ExamAttempt::where('status', 'corrige')
            ->whereHas('examen', fn($q) => $q->where('categorie_id', $categorieId))
            ->selectRaw('DISTINCT YEAR(date_fin) as annee')
            ->pluck('annee');

        // ===== Chart 1 : évolution par mois (année sélectionnée), catégorie du prof =====
        $attemptsAnnee = ExamAttempt::where('status', 'corrige')
            ->whereYear('date_fin', $anneeSelectionnee)
            ->whereHas('examen', fn($q) => $q->where('categorie_id', $categorieId))
            ->with('examen')
            ->get();

        $statistiquesParMois = collect(range(1, 12))->map(function ($mois) use ($attemptsAnnee) {
            $attemptsDuMois = $attemptsAnnee->filter(fn($a) => $a->date_fin?->month === $mois);

            $pourcentages = $attemptsDuMois
                ->map(fn($attempt) => $this->calculerPourcentageAttempt($attempt->examen, $attempt))
                ->filter(fn($p) => $p !== null);

            return [
                'mois' => \Carbon\Carbon::create()->month($mois)->translatedFormat('M'),
                'moyenne' => $pourcentages->isNotEmpty() ? round($pourcentages->avg(), 1) : null,
            ];
        });

        // ===== Chart 2 : moyenne générale (toutes années confondues), catégorie du prof =====
        $tousLesAttemptsCorriges = ExamAttempt::where('status', 'corrige')
            ->whereHas('examen', fn($q) => $q->where('categorie_id', $categorieId))
            ->with('examen')
            ->get();

        $toutesLesMoyennes = $tousLesAttemptsCorriges
            ->map(fn($attempt) => $this->calculerPourcentageAttempt($attempt->examen, $attempt))
            ->filter(fn($p) => $p !== null);

        $moyenneGenerale = $toutesLesMoyennes->isNotEmpty() ? round($toutesLesMoyennes->avg(), 1) : null;

        $nouveauExamens = Examen::where('categorie_id', $categorieId)
            ->where('status', 'brouillon')
            ->get();

        $examenPublies = Examen::where('categorie_id', $categorieId)
            ->where('status', '!=','brouillon')
            ->orderBy('id', 'desc')
            ->get();

        return view('prof.dashboard', compact(
            'totalEtudiants', 'totalExamens',
            'statistiquesParMois', 'moyenneGenerale',
            'anneeSelectionnee', 'anneesDisponibles',
            'nouveauExamens', 'examenPublies','slug'
        ));
    }
}
