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
            ->paginate(10);

        return view('prof.examen.show', compact('categorie', 'examens', 'slug'));
    }


    public function assignTypes(Examen $examen)
    {
        $typesExercice = TypeExercice::all();

        return view('prof.examen.assign-types', compact('examen', 'typesExercice'));
    }

    public function storeTypes(Request $request, Examen $examen)
    {
        $validated = $request->validate([
            'type_exercice_id' => ['required', 'array', 'min:1'],
            'type_exercice_id.*' => ['exists:types_exercice,id'],
            'ordre' => ['required', 'array'],
            'ordre.*' => ['required', 'integer', 'min:0'],
        ], [
            'type_exercice_id.required' => 'Veuillez sélectionner au moins un type d\'exercice.',
        ]);

        // Mamorona array miaraka amin'ny 'ordre' marina isaky ny type_exercice voafidy
        $syncData = [];
        foreach ($validated['type_exercice_id'] as $typeId) {
            $syncData[$typeId] = ['ordre' => $validated['ordre'][$typeId] ?? 0];
        }

        $examen->typesExercice()->sync($syncData);

        return redirect()
            ->back()
            ->with('success', 'Types d\'exercice ajoutés avec succès.');
    }
    

    public function showTypes(string $slug, Examen $examen)
    {
        $categorie = Categorie::where('slug', $slug)->firstOrFail();
        
        $examen->load('typesExercice');

        return view('prof.examen.examen-type', compact('examen', 'slug'));
    }
    
}
