<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\ExamAttempt;
use App\Models\Examen;
use App\Models\Prof;
use App\Models\Student;
use App\Traits\CalculeStatistiquesExamen;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDasboardController extends Controller
{
    use CalculeStatistiquesExamen;

    public function index(Request $request)
    {
        $totalEtudiants = Student::count();
        $totalProfs = Prof::count();
        $totalExamens = Examen::count();
        $totalCategories = Categorie::count();

        $anneeSelectionnee = $request->input('annee', now()->year);
        $moisSelectionne = $request->input('mois', now()->month);

        $anneesDisponibles = ExamAttempt::where('status', 'corrige')
            ->selectRaw('DISTINCT YEAR(date_fin) as annee')
            ->pluck('annee');

        $attemptsAnnee = ExamAttempt::where('status', 'corrige')
            ->whereYear('date_fin', $anneeSelectionnee)
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

        $tousLesAttemptsCorriges = ExamAttempt::where('status', 'corrige')->with('examen')->get();
        $toutesLesMoyennes = $tousLesAttemptsCorriges
            ->map(fn($attempt) => $this->calculerPourcentageAttempt($attempt->examen, $attempt))
            ->filter(fn($p) => $p !== null);
        $moyenneGenerale = $toutesLesMoyennes->isNotEmpty() ? round($toutesLesMoyennes->avg(), 1) : null;

        $attemptsDuMoisChoisi = ExamAttempt::where('status', 'corrige')
            ->whereYear('date_fin', $anneeSelectionnee)
            ->whereMonth('date_fin', $moisSelectionne)
            ->with('examen.categorie')
            ->get();

        $categories = Categorie::all();

        $statistiquesParCategorie = $categories->map(function ($categorie) use ($attemptsDuMoisChoisi) {
            $attemptsDeCetteCategorie = $attemptsDuMoisChoisi->filter(
                fn($a) => $a->examen->categorie_id === $categorie->id
            );

            $pourcentages = $attemptsDeCetteCategorie
                ->map(fn($attempt) => $this->calculerPourcentageAttempt($attempt->examen, $attempt))
                ->filter(fn($p) => $p !== null);

            return [
                'categorie' => $categorie->nom,
                'moyenne' => $pourcentages->isNotEmpty() ? round($pourcentages->avg(), 1) : null,
            ];
        })->filter(fn($s) => $s['moyenne'] !== null)->values();

        $attemptsDuMoisPourClassement = ExamAttempt::where('status', 'corrige')
            ->whereYear('date_fin', $anneeSelectionnee)
            ->whereMonth('date_fin', $moisSelectionne)
            ->with(['examen', 'student.user'])
            ->get();

        $top5Eleves = $attemptsDuMoisPourClassement
            ->map(function ($attempt) {
                return [
                    'student_id' => $attempt->student_id,
                    'nom' => $attempt->student?->user?->name,
                    'image' => $attempt->student?->user?->image,
                    'pourcentage' => $this->calculerPourcentageAttempt($attempt->examen, $attempt),
                ];
            })
            ->filter(fn($a) => $a['pourcentage'] !== null && $a['student_id'] !== null)
            ->groupBy('student_id')
            ->map(function ($groupe) {
                return [
                    'nom' => $groupe->first()['nom'],
                    'image' => $groupe->first()['image'],
                    'moyenne' => round($groupe->avg('pourcentage'), 1),
                ];
            })
            ->sortByDesc('moyenne')
            ->take(5)
            ->values();

        return view('admin.dashboard', compact(
            'totalEtudiants', 'totalProfs', 'totalExamens', 'totalCategories',
            'statistiquesParMois', 'moyenneGenerale', 'statistiquesParCategorie',
            'anneeSelectionnee', 'moisSelectionne', 'anneesDisponibles', 'top5Eleves'
        ));
    }
}
