<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Examen;
use App\Models\Text;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfExamenTextController extends Controller
{
    public function show(string $slug, Examen $examen)
    {
        $categorie = Categorie::where('slug', $slug)->firstOrFail();
        // $fichierWebs = Fichier::where('examen_id', $examen->id)->get();
        $texts = $examen->texts()
                    ->where('categorie_id', $categorie->id)
                    ->with('textQuestions')
                    ->latest()
                    ->get();

        return view('prof.examen.text.show', compact('slug','examen', 'texts'));
    }

    public function create(string $slug, Examen $examen)
    {
        return view('prof.examen.text.create', compact('slug', 'examen'));
    }

    public function store(Request $request, string $slug, Examen $examen)
    {
        $categorie = Categorie::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'texte' => ['required', 'string'],
            'duree_minutes' => ['nullable', 'integer', 'min:1'],
            'note_totale' => ['nullable', 'integer', 'min:1'],
        ], [
            'titre.required' => 'Le titre est obligatoire.',
            'texte.required' => 'Le texte à lire est obligatoire.',
            'duree_minutes.integer' => 'La durée doit être un nombre entier.',
            'note_totale.integer' => 'La note totale doit être un nombre entier.',
        ]);

        $text = Text::create([
            'examen_id' => $examen->id,
            'categorie_id' => $categorie->id,
            'titre' => $validated['titre'],
            'texte' => $validated['texte'],
            'duree_minutes' => $validated['duree_minutes'] ?? null,
            'note_totale' => $validated['note_totale'] ?? null,
            'ordre' => Text::where('examen_id', $examen->id)->count(),
        ]);

        return redirect()
            ->route('prof.examen.text.question.show', [$slug, $examen->id, $text->id])
            ->with('success', 'Texte créé avec succès. Vous pouvez maintenant ajouter des questions.');
    }

    public function edit(string $slug, Examen $examen, Text $text)
    {
        return view('prof.examen.text.edit', compact('slug', 'examen', 'text'));
    }

    public function update(Request $request, string $slug, Examen $examen, Text $text)
    {
        $validated = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'texte' => ['required', 'string'],
            'duree_minutes' => ['nullable', 'integer', 'min:1'],
            'note_totale' => ['nullable', 'integer', 'min:1'],
        ], [
            'titre.required' => 'Le titre est obligatoire.',
            'texte.required' => 'Le texte à lire est obligatoire.',
            'duree_minutes.integer' => 'La durée doit être un nombre entier.',
            'note_totale.integer' => 'La note totale doit être un nombre entier.',
        ]);

        $text->update([
            'titre' => $validated['titre'],
            'texte' => $validated['texte'],
            'duree_minutes' => $validated['duree_minutes'] ?? null,
            'note_totale' => $validated['note_totale'] ?? null,
        ]);

        return redirect()
            ->route('prof.examen.text', [$slug, $examen->id])
            ->with('success', 'Texte modifié avec succès.');
    }

    public function destroy(string $slug, Examen $examen, Text $text)
    {
        $text->delete();

        return redirect()->back()
            ->with('success', 'Texte supprimé avec succès.');
    }
}
