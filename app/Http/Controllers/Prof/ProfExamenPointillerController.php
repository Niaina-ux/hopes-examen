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

    public function create(string $slug, Examen $examen)
    {
        return view('prof.examen.pointiller.create', compact('slug','examen'));
    }

    public function store(Request $request, string $slug, Examen $examen)
    {
        $categorie = Categorie::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'duree_minutes' => ['nullable', 'integer', 'min:1'],
            'note_totale' => ['nullable', 'integer', 'min:1'],
        ], [
            'titre.required' => 'Le titre est obligatoire.',
            'duree_minutes.integer' => 'La durée doit être un nombre entier.',
            'note_totale.integer' => 'La note totale doit être un nombre entier.',
        ]);

        $pointille = Pointiller::create([
            'examen_id' => $examen->id,
            'categorie_id' => $categorie->id,
            'titre' => $validated['titre'],
            'description' => $validated['description'] ?? null,
            'duree_minutes' => $validated['duree_minutes'] ?? null,
            'note_totale' => $validated['note_totale'] ?? null,
            'ordre' => Pointiller::where('examen_id', $examen->id)->count(),
        ]);

        return redirect()
            ->route('prof.examen.pointiller', [$slug, $examen->id])
            ->with('success', 'Exercice compoléter le pointiller créé avec succès. Vous pouvez maintenant ajouter des questions.');
    }

    public function edit(string $slug, int $examenId, int $pointillerId)
    {
        $examen = Examen::find($examenId);

        if (!$examen) {
            return redirect()
                ->route('prof.examen.show', $slug)
                ->with('error', "Il y a un problème dans l'URL !");
        }

        $pointiller = Pointiller::where('examen_id', $examen->id)->find($pointillerId);

        if (!$pointiller) {
            return redirect()
                ->route('prof.examen.pointiller', [$slug, $examen->id])
                ->with('error', "Cet exercice de pointillé est introuvable pour cet examen.");
        }

        return view('prof.examen.pointiller.edit', compact('slug', 'examen', 'pointiller'));
    }

    public function update(Request $request, string $slug, Examen $examen, Pointiller $pointiller)
    {
        $validated = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'duree_minutes' => ['nullable', 'integer', 'min:1'],
            'note_totale' => ['nullable', 'integer', 'min:1'],
        ], [
            'titre.required' => 'Le titre est obligatoire.',
            'duree_minutes.integer' => 'La durée doit être un nombre entier.',
            'note_totale.integer' => 'La note totale doit être un nombre entier.',
        ]);

        $pointiller->update([
            'titre' => $validated['titre'],
            'description' => $validated['description'] ?? null,
            'duree_minutes' => $validated['duree_minutes'] ?? null,
            'note_totale' => $validated['note_totale'] ?? null,
        ]);

        return redirect()
            ->route('prof.examen.pointiller', [$slug, $examen->id])
            ->with('success', 'Exercice Compléter le pointiller modifié avec succès.');
    }
}
