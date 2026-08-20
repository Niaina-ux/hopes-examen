<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Code;
use App\Models\ExamAttempt;
use App\Models\Examen;
use App\Models\Fichier;
use App\Models\GlisserDeposer;
use App\Models\ImageExercice;
use App\Models\MotsCroises;
use App\Models\Pointiller;
use App\Models\Qcm;
use App\Models\Redaction;
use App\Models\Relier;
use App\Models\Text;
use App\Models\TypeExercice;
use Illuminate\Http\Request;

class ProfExamenController extends Controller
{


    public function show(Request $request, string $slug)
    {
        $categorie = Categorie::with('typesExerciceAutorises')
            ->where('slug', $slug)
            ->firstOrFail();

        $typePremier = Categorie::where('slug', $slug)
            ->firstOrFail()
            ->typesExerciceAutorises()
            ->first();

        $moisSelectionne = $request->input('mois');
        $dateSelectionnee = $request->input('date');
        $modeTous = !$moisSelectionne && !$dateSelectionnee;

        if ($dateSelectionnee) {
            $moisSelectionne = \Carbon\Carbon::parse($dateSelectionnee)
                ->format('Y-m');
            $modeTous = false;
        }

        $datesDisponibles = collect();

        if ($moisSelectionne) {
            $datesDisponibles = Examen::where(
                'categorie_id',
                $categorie->id
            )
                ->whereNotNull('date_examen')
                ->whereYear(
                    'date_examen',
                    substr($moisSelectionne, 0, 4)
                )
                ->whereMonth(
                    'date_examen',
                    substr($moisSelectionne, 5, 2)
                )
                ->selectRaw('DATE(date_examen) as date')
                ->distinct()
                ->orderByDesc('date')
                ->pluck('date')
                ->map(fn ($date) => \Carbon\Carbon::parse($date)
                    ->format('Y-m-d'))
                ->values();
        }

        if (
            !$modeTous &&
            !$dateSelectionnee &&
            $datesDisponibles->isNotEmpty()
        ) {
            $dateSelectionnee = $datesDisponibles->first();
        }

        $query = Examen::where(
            'categorie_id',
            $categorie->id
        );

        if ($dateSelectionnee) {
            $query->whereDate(
                'date_examen',
                $dateSelectionnee
            );
        } elseif ($moisSelectionne) {
            $query
                ->whereYear(
                    'date_examen',
                    substr($moisSelectionne, 0, 4)
                )
                ->whereMonth(
                    'date_examen',
                    substr($moisSelectionne, 5, 2)
                );
        }

        $examens = $query
            ->orderByDesc('date_examen')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('prof.examen.show', compact(
            'categorie',
            'examens',
            'slug',
            'datesDisponibles',
            'dateSelectionnee',
            'moisSelectionne',
            'modeTous',
            'typePremier'
        ));
    }

    public function assignTypes(string $slug, int $examenId)
    {
        $examen = Examen::find($examenId);
        if (!$examen) {
            return redirect()
                ->route('prof.examen.show', $slug)
                ->with('error', "Il y a un problème dans l'URL !");
        }
        $examen->loadMissing('categorie');
        $typesExercice = $examen->categorie->typesExerciceAutorises;
        return view('prof.examen.assign-types', compact('slug', 'examen', 'typesExercice'));
    }

    public function storeTypes(Request $request, string $slug,  Examen $examen)
    {
        $validated = $request->validate([
            'type_exercice_id'   => ['required', 'array', 'min:1'],
            'type_exercice_id.*' => ['exists:types_exercice,id'],
            'ordre'              => ['required', 'array'],
            'ordre.*'            => ['required', 'integer', 'min:0'],
        ], [
            'type_exercice_id.required' => 'Veuillez sélectionner au moins un type d\'exercice.',
            'ordre.required'            => 'Veuillez indiquer l\'ordre pour chaque type d\'exercice sélectionné.',
        ]); 
        $ordresSelectionnes = [];
        foreach ($validated['type_exercice_id'] as $typeId) {
            if (!isset($validated['ordre'][$typeId]) || $validated['ordre'][$typeId] === '' || $validated['ordre'][$typeId] === null) {
                return back()->withErrors([
                    'ordre' => "Veuillez indiquer l'ordre pour chaque type d'exercice sélectionné.",
                ])->withInput();
            }
            $ordresSelectionnes[] = (int) $validated['ordre'][$typeId];
        }

        
        if (count($ordresSelectionnes) !== count(array_unique($ordresSelectionnes))) {
            return back()->withErrors([
                'ordre' => 'Deux types d\'exercice ne peuvent pas avoir le même ordre. Veuillez attribuer un ordre unique à chacun.',
            ])->withInput();
        }
        $syncData = [];
        foreach ($validated['type_exercice_id'] as $typeId) {
            $syncData[$typeId] = ['ordre' => $validated['ordre'][$typeId]];
        }
        $examen->typesExercice()->sync($syncData);
        return redirect()
            ->route('prof.examen.showtypes',[$slug, $examen->id] )
            ->with('success', 'Types d\'exercice ajoutés avec succès.');
    }
    

    public function showTypes(string $slug, int $examenId)
    {
        $categorie = Categorie::where('slug', $slug)->first();
        if (!$categorie) {
            return redirect()
                ->route('prof.examen.show', $slug)
                ->with('error', "Catégorie introuvable.");
        }
        $examen = Examen::find($examenId);
        if (!$examen) {
            return redirect()
                ->route('prof.examen.show', $slug)
                ->with('error', "Il y a un problème dans l'URL !");
        }
        $examen->load('typesExercice');

        return view('prof.examen.examen-type', compact('examen', 'slug'));
    }


    public function terminerCreation(string $slug, int $examenId)
    {
        $examen = Examen::find($examenId);
        if (!$examen) {
            return redirect()
                ->route('prof.examen.show', $slug)
                ->with('error', "Il y a un problème dans l'URL !");
        }
        $examen->load('typesExercice');

        $examen->update(['status' => 'publie']);

        return redirect()
            ->route('prof.examen.show', $slug)
            ->with('success', 'Examen finalisé avec succès.');
    }

    public function remettreEnBrouillon(string $slug, int $examenId)
    {
        $examen = Examen::find($examenId);

        if (!$examen) {
            return redirect()
                ->route('prof.examen.show', $slug)
                ->with('error', "Il y a un problème dans l'URL !");
        }

        $aDejaDesAttempts = ExamAttempt::where('examen_id', $examen->id)->exists();

        if ($aDejaDesAttempts) {
            return redirect()
                ->back()
                ->with('error', "Impossible de modifier : des étudiants ont déjà passé ou commencé cet examen.");
        }

        $examen->update(['status' => 'brouillon']);

        return redirect()
            ->back()
            ->with('success', "L'examen est repassé en mode modification.");
    }
    
}
