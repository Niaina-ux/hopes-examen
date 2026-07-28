<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use Illuminate\Http\Request;

class AdminCategoryController extends Controller
{
    public function index()
    {
        $categories = Categorie::latest()->paginate(10);
        return view('admin.category.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.category.create');
    }

    public function store(Request $request)
    {   
        $request->merge([
            'slug' => strtolower(trim($request->slug)),
        ]);
        
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'in:français,anglais,web,python,design,bureautique',
                'unique:categories,slug',
            ],
        ], [
            'nom.required' => 'Le titre est obligatoire.',
            'slug.required' => 'Le slug est obligatoire.',
            'slug.in' => 'Le slug doit être l\'un des suivants : français, anglais, web, python, design, bureautique.',
            'slug.unique' => 'Cette catégorie existe déjà.',
        ]);

        Categorie::create([
            'nom' => trim($validated['nom']),
            'slug' => trim(strtolower($validated['slug'])),
        ]);

        return redirect()
            ->route('admin.categorie.index')
            ->with('success', 'Catégorie créée avec succès.');
    }

    public function edit(Categorie $categorie)
    {
        return view('admin.category.edit', compact('categorie'));
    }

    public function update(Request $request, Categorie $categorie)
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'in:francais,anglais,web,python,design,bureautique',
                'unique:categories,slug,' . $categorie->id,
            ],
        ], [
            'nom.required' => 'Le titre est obligatoire.',
            'slug.required' => 'Le slug est obligatoire.',
            'slug.in' => 'Le slug doit être l\'un des suivants : francais, anglais, web, python, design, bureautique.',
            'slug.unique' => 'Cette catégorie existe déjà.',
        ]);

        $categorie->update([
            'nom' => trim($validated['nom']),
            'slug' => trim(strtolower($validated['slug'])),
        ]);

        return redirect()
            ->route('admin.categorie.index')
            ->with('success', 'Catégorie modifiée avec succès.');
    }

    public function destroy(Categorie $categorie)
    {
        $categorie->delete();

        return redirect()
            ->route('admin.categorie.index')
            ->with('success', 'Catégorie supprimée.');
    }
}
