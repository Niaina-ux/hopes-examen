<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Examen;
use App\Models\Fichier;
use Illuminate\Http\Request;

class ProfExamenFichierController extends Controller
{
    public function show(string $slug, Examen $examen)
    {
        $categorie = Categorie::where('slug', $slug)->firstOrFail();
        // $fichierWebs = Fichier::where('examen_id', $examen->id)->get();
        $fichiers = $examen->fichier()
                    ->where('categorie_id', $categorie->id)
                    ->with('fichierQuestions')
                    ->latest()
                    ->get();

        return view('prof.examen.downloadUpload.show', compact('slug','examen', 'fichiers'));
    }

    public function create(string $slug, Examen $examen)
    {
        return view('prof.examen.downloadUpload.create', compact('slug','examen'));
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

        $fichiers = Fichier::create([
            'examen_id' => $examen->id,
            'categorie_id' => $categorie->id,
            'titre' => $validated['titre'],
            'description' => $validated['description'] ?? null,
            'duree_minutes' => $validated['duree_minutes'] ?? null,
            'note_totale' => $validated['note_totale'] ?? null,
            'ordre' => Fichier::where('examen_id', $examen->id)->count(),
        ]);

        return redirect()
            ->route('prof.examen.fichier', [$slug, $examen->id])
            ->with('success', 'Exercice compoléter le pointiller créé avec succès. Vous pouvez maintenant ajouter des questions.');
    }

    public function edit(string $slug, Examen $examen, Fichier $fichier)
    {
        return view('prof.examen.downloadUpload.edit', compact('slug', 'examen', 'fichier'));
    }

    public function update(Request $request, string $slug, Examen $examen, Fichier $fichier)
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

        $fichier->update([
            'titre' => $validated['titre'],
            'description' => $validated['description'] ?? null,
            'duree_minutes' => $validated['duree_minutes'] ?? null,
            'note_totale' => $validated['note_totale'] ?? null,
        ]);

        return redirect()
            ->route('prof.examen.fichier', [$slug, $examen->id])
            ->with('success', 'Exercice download & upload modifié avec succès.');
    }

    public function destroy(string $slug, Examen $examen, Fichier $fichier)
    {
        foreach ($fichier->fichierQuestions as $question) {
            if ($question->image && file_exists(public_path('images/questions/' . $question->image))) {
                unlink(public_path('images/questions/' . $question->image));
            }
            if ($question->video && file_exists(public_path('videos/questions/' . $question->video))) {
                unlink(public_path('videos/questions/' . $question->video));
            }
        }

        $fichier->delete();

        return redirect()->back()
            ->with('success', 'Dowload&Upload supprimé avec succès.');
    }
}
