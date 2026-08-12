<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Examen;
use App\Models\GlisserDeposer;
use Illuminate\Http\Request;

class ProfExamenGlisserDeposerController extends Controller
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

        $exercices = GlisserDeposer::where('examen_id', $examen->id)
            ->where('categorie_id', $categorie->id)
            ->with('questions.zones.item')
            ->orderBy('ordre')
            ->get();

        return view('prof.examen.glisserdeposer.show', compact('slug', 'examen', 'exercices'));
    }

    public function create(string $slug, Examen $examen)
    {
        return view('prof.examen.glisserdeposer.create', compact('slug', 'examen'));
    }

    public function store(Request $request, string $slug, Examen $examen)
    {
        $categorie = Categorie::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'titre'         => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'duree_minutes' => ['nullable', 'integer', 'min:1'],
            'note_totale'   => ['nullable', 'numeric', 'min:0.1'],
        ], [
            'titre.required' => 'Le titre est obligatoire.',
        ]);

        $dernierOrdre = GlisserDeposer::where('examen_id', $examen->id)->max('ordre') ?? 0;

        $exercice = GlisserDeposer::create([
            'examen_id'     => $examen->id,
            'categorie_id'  => $categorie->id,
            'titre'         => $validated['titre'],
            'description'   => $validated['description'] ?? null,
            'duree_minutes' => $validated['duree_minutes'] ?? null,
            'note_totale'   => $validated['note_totale'] ?? null,
            'ordre'         => $dernierOrdre + 1,
        ]);

        return redirect()
            ->route('prof.examen.glisserdeposer', [$slug, $examen->id])
            ->with('success', 'Exercice créé avec succès. Ajoutez maintenant des questions.');
    }

    public function edit(string $slug, int $examenId, int $glisserdeposerId)
    {
        $examen = Examen::find($examenId);

        if (!$examen) {
            return redirect()
                ->route('prof.examen.show', $slug)
                ->with('error', "Il y a un problème dans l'URL !");
        }

        $glisserdeposer = GlisserDeposer::where('examen_id', $examen->id)->find($glisserdeposerId);

        if (!$glisserdeposer) {
            return redirect()
                ->route('prof.examen.glisserdeposer', [$slug, $examen->id])
                ->with('error', "Cet exercice de glisser-déposer est introuvable pour cet examen.");
        }

        return view('prof.examen.glisserdeposer.edit', compact('slug', 'examen', 'glisserdeposer'));
    }

    public function update(Request $request, string $slug, Examen $examen, GlisserDeposer $glisserdeposer)
    {
        $validated = $request->validate([
            'titre'         => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'duree_minutes' => ['nullable', 'integer', 'min:1'],
            'note_totale'   => ['nullable', 'numeric', 'min:0.1'],
        ], [
            'titre.required' => 'Le titre est obligatoire.',
        ]);

        $glisserdeposer->update($validated);

        return redirect()
            ->route('prof.examen.glisserdeposer', [$slug, $examen->id])
            ->with('success', 'Exercice modifié avec succès.');
    }

    public function destroy(string $slug, Examen $examen, GlisserDeposer $glisserdeposer)
    {
        // Manafa ny sary rehetra an'ny question ao anatiny alohan'ny hamafana
        foreach ($glisserdeposer->questions as $question) {
            if ($question->image && file_exists(public_path('images/glisserdeposer/' . $question->image))) {
                unlink(public_path('images/glisserdeposer/' . $question->image));
            }
        }

        $glisserdeposer->delete();

        return redirect()
            ->back()
            ->with('success', 'Exercice supprimé avec succès.');
    }
}
