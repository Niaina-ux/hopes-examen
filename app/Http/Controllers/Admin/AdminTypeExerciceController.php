<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\TypeExercice;
use Illuminate\Http\Request;

class AdminTypeExerciceController extends Controller
{
    public function index(Request $request)
    {
        $categories = Categorie::orderBy('nom')->get();
        $categorieSlug = $request->input('categorie'); 

        if ($categorieSlug) {
            $categorie = Categorie::where('slug', $categorieSlug)->firstOrFail();
            $typesExercice = $categorie->typesExerciceAutorises()->paginate(10);
        } else {
            $categorie = null;
            $typesExercice = TypeExercice::paginate(10);
        }

        return view('admin.type-exercice.index', compact('typesExercice', 'categories', 'categorieSlug', 'categorie'));
    }

    public function create()
    {
        return view('admin.type-exercice.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:types_exercice,slug'],
            'icone' => ['nullable', 'string', 'max:255'],
        ], [
            'nom.required' => 'Le nom est obligatoire.',
            'slug.required' => 'Le slug est obligatoire.',
            'slug.unique' => 'Ce slug est déjà utilisé.',
        ]);

        TypeExercice::create([
            'nom' => trim($validated['nom']),
            'slug' => trim(strtolower($validated['slug'])),
            'icone' => $validated['icone'] ?? null,
        ]);

        return redirect()
            ->route('admin.typeExercice.index')
            ->with('success', 'Type d\'exercice créé avec succès.');
    }

    public function edit(TypeExercice $typeExercice)
    {
        return view('admin.type-exercice.edit', compact('typeExercice'));
    }

    public function update(Request $request, TypeExercice $typeExercice)
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:types_exercice,slug,' . $typeExercice->id],
            'icone' => ['nullable', 'string', 'max:255'],
        ], [
            'nom.required' => 'Le nom est obligatoire.',
            'slug.required' => 'Le slug est obligatoire.',
            'slug.unique' => 'Ce slug est déjà utilisé.',
        ]);

        $typeExercice->update([
            'nom' => trim($validated['nom']),
            'slug' => trim(strtolower($validated['slug'])),
            'icone' => $validated['icone'] ?? null,
        ]);

        return redirect()
            ->route('admin.typeExercice.index')
            ->with('success', 'Type d\'exercice modifié avec succès.');
    }

    public function destroy(TypeExercice $typeExercice)
    {
        $typeExercice->delete();

        return redirect()
            ->route('admin.typeExercice.index')
            ->with('success', 'Type d\'exercice supprimé.');
    }


    public function editTypesExercice(Categorie $categorie)
    {
        $categorie->loadMissing('typesExerciceAutorises');
        $typesExerciceTous = TypeExercice::orderBy('nom')->get();

        return view('admin.type-exercice.types-exercice', compact('categorie', 'typesExerciceTous'));
    }

    public function updateTypesExercice(Request $request, Categorie $categorie)
    {
        $validated = $request->validate([
            'types' => ['nullable', 'array'],
            'types.*' => ['integer', 'exists:types_exercice,id'],
        ]);

        $categorie->typesExerciceAutorises()->sync($validated['types'] ?? []);

        return redirect()
            ->route('admin.typeExercice.index', ['categorie' => $categorie->slug])
            ->with('success', "Types d'exercice mis à jour pour {$categorie->nom}.");
    }
}
