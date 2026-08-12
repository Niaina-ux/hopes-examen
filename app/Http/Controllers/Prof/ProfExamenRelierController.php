<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Examen;
use App\Models\Relier;
use Illuminate\Http\Request;

class ProfExamenRelierController extends Controller
{
    public function show(string $slug, int $examenId)
    {
        $examen = Examen::find($examenId);

        if (!$examen) {
            return redirect()
                ->route('prof.examen.show', $slug)
                ->with('error', "Il y a un problème dans l'URL !");
        }

        $reliers = $examen->relier()
            ->with('relierQuestions')
            ->latest()
            ->get();

        return view('prof.examen.relier.show', compact('slug', 'examen', 'reliers'));
    }

    public function create(string $slug, Examen $examen, )
    {
        return view('prof.examen.relier.create', compact('slug','examen'));
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

        $relier = Relier::create([
            'examen_id' => $examen->id,
            'categorie_id' => $categorie->id,
            'titre' => $validated['titre'],
            'description' => $validated['description'] ?? null,
            'duree_minutes' => $validated['duree_minutes'] ?? null,
            'note_totale' => $validated['note_totale'] ?? null,
            'ordre' => Relier::where('examen_id', $examen->id)->count(),
        ]);

        return redirect()
            ->route('prof.examen.relier', [$slug, $examen->id])
            ->with('success', 'QCM créé avec succès. Vous pouvez maintenant ajouter des questions.');
    }

    public function edit(string $slug, Examen $examenId, Relier $relierId)
    {
        $examen = Examen::find($examenId);

        if (!$examen) {
            return redirect()
                ->route('prof.examen.show', $slug)
                ->with('error', "Il y a un problème dans l'URL !");
        }

        $relier = Relier::where('examen_id', $examen->id)->find($relierId);

        if (!$relier) {
            return redirect()
                ->route('prof.examen.relier', [$slug, $examen->id])
                ->with('error', "Cette rélier est introuvable pour cet examen.");
        }

        return view('prof.examen.relie.edit', compact('slug', 'examen', 'relier'));
    }

    public function update(Request $request, string $slug, Examen $examen, Relier $relier)
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

        $relier->update([
            'titre' => $validated['titre'],
            'description' => $validated['description'] ?? null,
            'duree_minutes' => $validated['duree_minutes'] ?? null,
            'note_totale' => $validated['note_totale'] ?? null,
        ]);

        return redirect()
            ->route('prof.examen.relie.question.show', [$slug, $examen->id, $relier->id])
            ->with('success', 'Relier modifié avec succès.');
    }

    public function destroy(string $slug,  Examen $examen, Relier $relier)
    {
        foreach ($relier->relierQuestions as $question) {
            if ($question->image && file_exists(public_path('images/questions/' . $question->image))) {
                unlink(public_path('images/questions/' . $question->image));
            }
            if ($question->video && file_exists(public_path('videos/questions/' . $question->video))) {
                unlink(public_path('videos/questions/' . $question->video));
            }
        }

        $relier->delete();

        return redirect()->back()
            ->with('success', 'Relier supprimé avec succès.');
    }
}
