<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Code;
use App\Models\Examen;
use Illuminate\Http\Request;

class ProfExamenCodeController extends Controller
{
    public function show(string $slug, int $examenId)
    {
        $examen = Examen::find($examenId);

        if (!$examen) {
            return redirect()
                ->route('prof.examen.show', $slug)
                ->with('error', "Il y a un problème dans l'URL !");
        }

        $codes = $examen->code()
            ->with('codeQuestions')
            ->latest()
            ->get();

        return view('prof.examen.code.show', compact('slug', 'examen', 'codes'));
    }

    public function create(string $slug, Examen $examen)
    {
        return view('prof.examen.code.create', compact('examen','slug'));
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

        $codes = Code::create([
            'examen_id' => $examen->id,
            'categorie_id' => $categorie->id,
            'titre' => $validated['titre'],
            'description' => $validated['description'] ?? null,
            'duree_minutes' => $validated['duree_minutes'] ?? null,
            'note_totale' => $validated['note_totale'] ?? null,
            'ordre' => Code::where('examen_id', $examen->id)->count(),
        ]);

        return redirect()
            ->back()
            ->with('success', 'Exercice code créé avec succès. Vous pouvez maintenant ajouter des questions.');
    }


    public function edit(string $slug, int $examenId, int $codeId)
    {
        $examen = Examen::find($examenId);

        if (!$examen) {
            return redirect()
                ->route('prof.examen.show', $slug)
                ->with('error', "Il y a un problème dans l'URL !");
        }

        $code = Code::where('examen_id', $examen->id)->find($codeId);

        if (!$code) {
            return redirect()
                ->route('prof.examen.code', [$slug, $examen->id])
                ->with('error', "Cet exercice de code est introuvable pour cet examen.");
        }

        return view('prof.examen.code.edit', compact('slug', 'examen', 'code'));
    }

    public function update(Request $request, string $slug, Examen $examen, Code $code)
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

        $code->update([
            'titre' => $validated['titre'],
            'description' => $validated['description'] ?? null,
            'duree_minutes' => $validated['duree_minutes'] ?? null,
            'note_totale' => $validated['note_totale'] ?? null,
        ]);

        return redirect()
            ->route('prof.examen.code', [$slug, $examen->id])
            ->with('success', 'Exercice code modifié avec succès.');
    }


    public function destroy(string $slug, Examen $examen, Code $code)
    {
        $code->delete();
        return redirect()->back()
            ->with('success', 'Exercice  code supprimé avec succès.');
    }
}
