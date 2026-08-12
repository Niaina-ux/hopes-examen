<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Examen;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminExamenController extends Controller
{
    public function show(string $slug)
    {
        $categorie = Categorie::where('slug', $slug)->firstOrFail();

        $examens = Examen::where('categorie_id', $categorie->id)
            ->withCount('typesExercice')
            ->latest()
            ->paginate(10);

        return view('admin.examen.show', compact('categorie', 'examens', 'slug'));
    }


    public function create(string $slug)
    {
        $categorie = Categorie::where('slug', $slug)->firstOrFail();

        return view('admin.examen.create', compact('categorie', 'slug'));
    }

    public function store(Request $request, string $slug)
    {
        $categorie = Categorie::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'titre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('examens', 'titre')->where('categorie_id', $categorie->id),
            ],
            'description' => ['nullable', 'string'],
            'duree_minutes' => ['nullable', 'integer', 'min:1'],
        ], [
            'titre.required' => 'Le titre est obligatoire.',
            'titre.unique' => 'Un examen avec ce titre existe déjà dans cette catégorie.',
            'duree_minutes.integer' => 'La durée doit être un nombre entier.',
        ]);

        $examen = Examen::create([
            'titre' => $validated['titre'],
            'description' => $validated['description'] ?? null,
            'categorie_id' => $categorie->id,
            'duree_minutes' => $validated['duree_minutes'] ?? null,
        ]);

        return redirect()
            ->route('admin.examen.show', $slug)
            ->with('success', 'Examen créé avec succès.');
    }

    public function edit(string $slug, Examen $examen)
    {
        return view('admin.examen.edit', compact('slug', 'examen'));
    }

    public function update(Request $request, string $slug, Examen $examen)
    {
        $validated = $request->validate([
            'titre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('examens', 'titre')
                    ->where('categorie_id', $examen->categorie_id)
                    ->ignore($examen->id),
            ],
            'description'   => ['nullable', 'string'],
            'duree_minutes' => ['nullable', 'integer', 'min:1'],
            'status'        => ['required', 'in:brouillon,publie,archive'],
        ], [
            'titre.required' => 'Le titre est obligatoire.',
            'titre.unique'   => 'Un examen avec ce titre existe déjà dans cette catégorie.',
        ]);

        if ($validated['status'] === 'publie' && $examen->status !== 'archive') {
            return back()->withErrors([
                'status' => 'Vous ne pouvez publier un examen que s\'il est finalisé (archivé). Veuillez d\'abord terminer sa création.',
            ])->withInput();
        }

        $examen->update([
            'titre'         => $validated['titre'],
            'description'   => $validated['description'] ?? null,
            'duree_minutes' => $validated['duree_minutes'] ?? null,
            'status'        => $validated['status'],
        ]);

        return redirect()
            ->route('admin.examen.show', $slug)
            ->with('success', 'Examen modifié avec succès.');
    }

    public function destroy(string $slug, Examen $examen)
    {
        $examen->delete();

        return redirect()
            ->route('admin.examen.show', $slug)
            ->with('success', 'Examen supprimé avec succès.');
    }

}
