<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Examen;
use App\Models\Qcm;
use Illuminate\Http\Request;

class ProfExamenQcmController extends Controller
{
    public function show(string $slug, int $examenId)
    {
        $examen = Examen::find($examenId);

        if (!$examen) {
            return redirect()
                ->route('prof.examen.show', $slug)
                ->with('error', "Il y a un problème dans l'URL !");
        }

        $qcms = $examen->qcm()
            ->with('qcmQuestions')
            ->latest()
            ->get();

        return view('prof.examen.qcm.show', compact('examen', 'qcms', 'slug'));
    }

    public function create(string $slug, Examen $examen)
    {
        return view('prof.examen.qcm.create', compact('slug','examen'));
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

        // Fanamarinana: mijery raha mihoatra ny duree_minutes an'ny examen ny SUM duree_minutes
        if (!empty($validated['duree_minutes'])) {
            $dureeExistante = $examen->qcm()->sum('duree_minutes');
            $dureeTotal = $dureeExistante + $validated['duree_minutes'];

            if ($examen->duree_minutes !== null && $dureeTotal > $examen->duree_minutes) {
                return back()->withErrors([
                    'duree_minutes' => "La durée totale ({$dureeTotal} min) dépasse la durée autorisée pour cet examen ({$examen->duree_minutes} min). Durée déjà utilisée : {$dureeExistante} min.",
                ])->withInput();
            }
        }

        $qcm = Qcm::create([
            'examen_id' => $examen->id,
            'categorie_id' => $categorie->id,
            'titre' => $validated['titre'],
            'description' => $validated['description'] ?? null,
            'duree_minutes' => $validated['duree_minutes'] ?? null,
            'note_totale' => $validated['note_totale'] ?? null,
            'ordre' => Qcm::where('examen_id', $examen->id)->count(),
        ]);

        return redirect()
            ->route('prof.examen.qcm', [$slug, $examen->id, $qcm->id])
            ->with('success', 'QCM créé avec succès. Vous pouvez maintenant ajouter des questions.');
    }

    public function edit(string $slug, int $examenId, int $qcmId)
    {
        $examen = Examen::find($examenId);

        if (!$examen) {
            return redirect()
                ->route('prof.examen.show', $slug)
                ->with('error', "Il y a un problème dans l'URL !");
        }

        $qcm = Qcm::where('examen_id', $examen->id)->find($qcmId);

        if (!$qcm) {
            return redirect()
                ->route('prof.examen.qcm', [$slug, $examen->id])
                ->with('error', "Ce QCM est introuvable pour cet examen.");
        }

        return view('prof.examen.qcm.edit', compact('slug', 'examen', 'qcm'));
    }

    public function update(Request $request, string $slug, Examen $examen, Qcm $qcm)
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

        $qcm->update([
            'titre' => $validated['titre'],
            'description' => $validated['description'] ?? null,
            'duree_minutes' => $validated['duree_minutes'] ?? null,
            'note_totale' => $validated['note_totale'] ?? null,
        ]);

        return redirect()
            ->route('prof.examen.qcm', [$slug, $examen->id, $qcm->id])
            ->with('success', 'QCM modifié avec succès.');
    }

    public function destroy(string $slug,  Examen $examen, Qcm $qcm)
    {
        foreach ($qcm->qcmQuestions as $question) {
            if ($question->image && file_exists(public_path('images/questions/' . $question->image))) {
                unlink(public_path('images/questions/' . $question->image));
            }
            if ($question->video && file_exists(public_path('videos/questions/' . $question->video))) {
                unlink(public_path('videos/questions/' . $question->video));
            }
        }

        $qcm->delete();

        return redirect()->back()
            ->with('success', 'QCM supprimé avec succès.');
    }
}
