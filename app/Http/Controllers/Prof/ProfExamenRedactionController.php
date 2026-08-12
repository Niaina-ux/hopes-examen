<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Examen;
use App\Models\Redaction;
use Illuminate\Http\Request;

class ProfExamenRedactionController extends Controller
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

        $redactions = Redaction::where('categorie_id', $categorie->id)
            ->where('examen_id', $examen->id)
            ->get();

        return view('prof.examen.redaction.show', compact('slug', 'examen', 'redactions'));
    }

    public function create(string $slug, Examen $examen)
    {
        return view('prof.examen.redaction.create', compact('slug', 'examen'));
    }

    /**
     * Mitahiry ny redaction vaovao
     */
    public function store(Request $request, string $slug, Examen $examen)
    {
        $validated = $request->validate([
            'titre'            => ['nullable', 'string', 'max:255'],
            'sujet'            => ['required', 'string'],
            'instruction'      => ['nullable', 'string'],
            'nombre_mots_min'  => ['nullable', 'integer', 'min:1'],
            'nombre_mots_max'  => ['nullable', 'integer'],
            'duree_minutes'    => ['nullable', 'integer', 'min:1'],
            'note_totale'      => ['nullable', 'numeric', 'min:0.1'],
        ], [
            'sujet.required'          => 'Le sujet est obligatoire.',
            'nombre_mots_max.gte'     => 'Le nombre de mots maximum doit être supérieur ou égal au minimum.',
        ]);

        $dernierOrdre = Redaction::where('examen_id', $examen->id)->max('ordre') ?? 0;

        Redaction::create([
            'examen_id'        => $examen->id,
            'categorie_id'     => $examen->categorie_id,
            'titre'            => $validated['titre'] ?? null,
            'sujet'            => $validated['sujet'],
            'instruction'      => $validated['instruction'] ?? null,
            'nombre_mots_min'  => $validated['nombre_mots_min'] ?? null,
            'nombre_mots_max'  => $validated['nombre_mots_max'] ?? null,
            'duree_minutes'    => $validated['duree_minutes'] ?? null,
            'note_totale'      => $validated['note_totale'] ?? null,
            'ordre'            => $dernierOrdre + 1,
        ]);

        return redirect()
            ->route('prof.examen.redaction', [$slug, $examen->id])
            ->with('success', 'Exercice de rédaction ajouté avec succès.');
    }

    /**
     * Mampiseho ny form fanovana
     */
    public function edit(string $slug, int $examenId, int $redactionId)
    {
        $examen = Examen::find($examenId);

        if (!$examen) {
            return redirect()
                ->route('prof.examen.show', $slug)
                ->with('error', "Il y a un problème dans l'URL !");
        }

        $redaction = Redaction::where('examen_id', $examen->id)->find($redactionId);

        if (!$redaction) {
            return redirect()
                ->route('prof.examen.redaction', [$slug, $examen->id])
                ->with('error', "Cette rédaction est introuvable pour cet examen.");
        }

        return view('prof.examen.redaction.edit', compact('slug', 'examen', 'redaction'));
    }

    /**
     * Manova ny redaction
     */
    public function update(Request $request, string $slug, Examen $examen, Redaction $redaction)
    {
        $validated = $request->validate([
            'titre'            => ['nullable', 'string', 'max:255'],
            'sujet'            => ['required', 'string'],
            'instruction'      => ['nullable', 'string'],
            'nombre_mots_min'  => ['nullable', 'integer', 'min:1'],
            'nombre_mots_max'  => ['nullable', 'integer'],
            'duree_minutes'    => ['nullable', 'integer', 'min:1'],
            'note_totale'      => ['nullable', 'numeric', 'min:0.1'],
        ], [
            'sujet.required'      => 'Le sujet est obligatoire.',
            'nombre_mots_max.gte' => 'Le nombre de mots maximum doit être supérieur ou égal au minimum.',
        ]);

        $redaction->update([
            'titre'            => $validated['titre'] ?? null,
            'sujet'            => $validated['sujet'],
            'instruction'      => $validated['instruction'] ?? null,
            'nombre_mots_min'  => $validated['nombre_mots_min'] ?? null,
            'nombre_mots_max'  => $validated['nombre_mots_max'] ?? null,
            'duree_minutes'    => $validated['duree_minutes'] ?? null,
            'note_totale'      => $validated['note_totale'] ?? null,
        ]);

        return redirect()
            ->route('prof.examen.redaction', [$slug, $examen->id])
            ->with('success', 'Exercice de rédaction modifié avec succès.');
    }

    public function detail(string $slug, Examen $examen, Redaction $redaction)
    {
        return view('prof.examen.redaction.detail', compact('slug', 'examen', 'redaction'));
    }

    /**
     * Mamafa ny redaction
     */
    public function destroy(string $slug, Examen $examen, Redaction $redaction)
    {
        $redaction->delete();

        return redirect()
            ->route('prof.examen.redaction', [$slug, $examen->id])
            ->with('success', 'Exercice de rédaction supprimé avec succès.');
    }
}
