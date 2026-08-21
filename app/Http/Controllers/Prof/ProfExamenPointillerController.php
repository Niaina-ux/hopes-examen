<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Examen;
use App\Models\Pointiller;
use App\Models\Relier;
use Illuminate\Http\Request;

class ProfExamenPointillerController extends Controller
{
    // -----------
    public function showbanque(string $slug)
    { 
        $categorie = Categorie::where('slug', $slug)->firstOrFail();
        $types = $categorie->typesExerciceAutorises;

        $pointillers = Pointiller::with('pointillerQuestions')
            ->latest()
            ->get();

        return view('prof.questions.pointiller.show', compact('types', 'slug', 'pointillers'));
    }

    public function create(string $slug )
    {
        return view('prof.questions.pointiller.create', compact('slug',));
    }

    public function store(Request $request, string $slug)
    {
        $categorie = Categorie::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ], [
            'titre.required' => 'Le titre est obligatoire.',
        ]);

        $pointille = Pointiller::create([
            'categorie_id' => $categorie->id,
            'titre' => $validated['titre'],
            'description' => $validated['description'] ?? null,
            'duree_minutes' => $validated['duree_minutes'] ?? null,
            'note_totale' => $validated['note_totale'] ?? null,
            'ordre' => 0,
        ]);

        return redirect()
            ->route('prof.question.pointiller', $slug)
            ->with('success', 'Exercice compoléter le pointiller créé avec succès. Vous pouvez maintenant ajouter des questions.');
    }

    public function edit(string $slug,  int $pointillerId)
    {

        $categorie = Categorie::where('slug', $slug)->firstOrFail();

        $pointiller = Pointiller::where('categorie_id', $categorie->id)->find($pointillerId);

        if (!$pointiller) {
            return redirect()
                ->route('prof.question.pointiller', $slug)
                ->with('error', "Ce exercice est introuvable pour cette catégorie.");
        }

        return view('prof.questions.pointiller.edit', compact('slug',  'pointiller'));
    }

    public function update(Request $request, string $slug, int $pointillerId)
    {
        $categorie = Categorie::where('slug', $slug)->firstOrFail();

        $pointiller = Pointiller::where('categorie_id', $categorie->id)->find($pointillerId);

        if (!$pointiller) {
            return redirect()
                ->route('prof.question.pointiller', $slug)
                ->with('error', "Ce exericice est introuvable pour cette catégorie.");
        }

        $validated = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ], [
            'titre.required' => 'Le titre est obligatoire.',
        ]);

        $pointiller->update([
            'titre' => $validated['titre'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('prof.question.pointiller', $slug)
            ->with('success', 'Relier par flèche modifié avec succès.');
    }


    public function destroy(string $slug,  int $pointillerId)
    {
        $categorie = Categorie::where('slug', $slug)->firstOrFail();

        $pointiller = Pointiller::where('categorie_id', $categorie->id)->find($pointillerId);

        if (!$pointiller) {
            return redirect()
                ->route('prof.question.pointiller', $slug)
                ->with('error', "Ce exericice est introuvable pour cette catégorie.");
        }

        $pointiller->delete();

        return redirect()->back()
            ->with('success', 'Relier supprimé avec succès.');
    }

    // -------------
    public function show(string $slug, int $examenId)
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

        $pointillers = $examen->pointiller()
            ->where('categorie_id', $categorie->id)
            ->with('pointillerQuestions')
            ->latest()
            ->get();

        return view('prof.examen.pointiller.show', compact('pointillers', 'examen', 'slug'));
    }
}
