<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Examen;
use App\Models\ImageExercice;
use Illuminate\Http\Request;

class ProfExamenImageController extends Controller
{
    public function show(string $slug, Examen $examen)
    {
        $categorie = Categorie::where('slug', $slug)->firstOrFail();

        $exercices = ImageExercice::where('examen_id', $examen->id)
            ->where('categorie_id', $categorie->id)
            ->with('questions')
            ->orderBy('ordre', 'desc')
            ->get();

        return view('prof.examen.image-exercice.show', compact('slug', 'examen', 'exercices'));
    }

    public function create(string $slug, Examen $examen)
    {
        return view('prof.examen.image-exercice.create', compact('slug', 'examen'));
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

        $dernierOrdre = ImageExercice::where('examen_id', $examen->id)->max('ordre') ?? 0;

        $exercice = ImageExercice::create([
            'examen_id'     => $examen->id,
            'categorie_id'  => $categorie->id,
            'titre'         => $validated['titre'],
            'description'   => $validated['description'] ?? null,
            'duree_minutes' => $validated['duree_minutes'] ?? null,
            'note_totale'   => $validated['note_totale'] ?? null,
            'ordre'         => $dernierOrdre + 1,
        ]);

        return redirect()
            ->route('prof.examen.image', [$slug, $examen->id])
            ->with('success', 'Exercice créé avec succès. Ajoutez maintenant des images.');
    }

    public function edit(string $slug, Examen $examen, ImageExercice $image)
    {
        return view('prof.examen.image.edit', compact('slug', 'examen', 'image'));
    }

    public function update(Request $request, string $slug, Examen $examen, ImageExercice $image)
    {
        $validated = $request->validate([
            'titre'         => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'duree_minutes' => ['nullable', 'integer', 'min:1'],
            'note_totale'   => ['nullable', 'numeric', 'min:0.1'],
        ], [
            'titre.required' => 'Le titre est obligatoire.',
        ]);

        $image->update($validated);

        return redirect()
            ->route('prof.examen.image', [$slug, $examen->id])
            ->with('success', 'Exercice modifié avec succès.');
    }

    public function destroy(string $slug, Examen $examen, ImageExercice $image)
    {
        foreach ($image->questions as $question) {
            if ($question->image && file_exists(public_path('images/exercice/' . $question->image))) {
                unlink(public_path('images/exercice/' . $question->image));
            }
        }

        $image->delete();

        return redirect()
            ->back()
            ->with('success', 'Exercice supprimé avec succès.');
    }

}
