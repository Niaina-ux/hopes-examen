<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Examen;
use App\Models\TypeExercice;
use Illuminate\Http\Request;

class ProfExamenController extends Controller
{

    public function show(string $slug)
    {
        $categorie = Categorie::where('slug', $slug)->firstOrFail();

        $examens = Examen::where('categorie_id', $categorie->id)
        ->orderBy('id', 'desc')
        ->paginate(10);

        return view('prof.examen.show', compact('categorie', 'examens', 'slug'));
    }


    public function assignTypes(string $slug, Examen $examen)
    {
        $typesExercice = TypeExercice::all();

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

        // Manangona ny "ordre" an'ireo type_exercice_id VOAFIDY IHANY
        $ordresSelectionnes = [];
        foreach ($validated['type_exercice_id'] as $typeId) {
            if (!isset($validated['ordre'][$typeId]) || $validated['ordre'][$typeId] === '' || $validated['ordre'][$typeId] === null) {
                return back()->withErrors([
                    'ordre' => "Veuillez indiquer l'ordre pour chaque type d'exercice sélectionné.",
                ])->withInput();
            }

            $ordresSelectionnes[] = (int) $validated['ordre'][$typeId];
        }

        // Manamarina fa tsy misy "ordre" mitovy eo amin'ireo VOAFIDY ihany
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
    

    public function showTypes(string $slug, Examen $examen)
    {
        $categorie = Categorie::where('slug', $slug)->firstOrFail();
        
        $examen->load('typesExercice');

        return view('prof.examen.examen-type', compact('examen', 'slug'));
    }
    
}
