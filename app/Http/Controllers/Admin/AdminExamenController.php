<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Examen;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminExamenController extends Controller
{
    public function show(Request $request, string $slug)
    {
        $categorie = Categorie::where('slug', $slug)->firstOrFail();

        $moisSelectionne = $request->input('mois');
        $dateSelectionnee = $request->input('date');

        $modeTous = !$moisSelectionne && !$dateSelectionnee;

        if ($dateSelectionnee) {
            $moisSelectionne = \Carbon\Carbon::parse($dateSelectionnee)
                ->format('Y-m');
            $modeTous = false;
        }

        $datesDisponibles = collect();

        if ($moisSelectionne) {
            $datesDisponibles = Examen::where(
                'categorie_id',
                $categorie->id
            )
                ->whereNotNull('date_examen')
                ->whereYear(
                    'date_examen',
                    substr($moisSelectionne, 0, 4)
                )
                ->whereMonth(
                    'date_examen',
                    substr($moisSelectionne, 5, 2)
                )
                ->selectRaw('DATE(date_examen) as date')
                ->distinct()
                ->orderByDesc('date')
                ->pluck('date')
                ->map(fn ($date) => \Carbon\Carbon::parse($date)
                    ->format('Y-m-d'))
                ->values();
        }

        if (
            !$modeTous &&
            !$dateSelectionnee &&
            $datesDisponibles->isNotEmpty()
        ) {
            $dateSelectionnee = $datesDisponibles->first();
        }

        $query = Examen::where(
            'categorie_id',
            $categorie->id
        )->withCount('typesExercice');

        if ($dateSelectionnee) {
            $query->whereDate(
                'date_examen',
                $dateSelectionnee
            );
        } elseif ($moisSelectionne) {
            $query
                ->whereYear(
                    'date_examen',
                    substr($moisSelectionne, 0, 4)
                )
                ->whereMonth(
                    'date_examen',
                    substr($moisSelectionne, 5, 2)
                );
        }

        $examens = $query
            ->latest('date_examen')
            ->paginate(10)
            ->withQueryString();

        return view('admin.examen.show', compact(
            'categorie',
            'examens',
            'slug',
            'datesDisponibles',
            'dateSelectionnee',
            'moisSelectionne',
            'modeTous'
        ));
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
            'date_examen' => ['nullable', 'date', 'after_or_equal:today'], // ✅ nouveau
        ], [
            'titre.required' => 'Le titre est obligatoire.',
            'titre.unique' => 'Un examen avec ce titre existe déjà dans cette catégorie.',
            'duree_minutes.integer' => 'La durée doit être un nombre entier.',
            'date_examen.date' => 'La date doit être une date valide.',
            'date_examen.after_or_equal' => "La date de l'examen ne peut pas être dans le passé.",
        ]);

        $examen = Examen::create([
            'titre' => $validated['titre'],
            'description' => $validated['description'] ?? null,
            'categorie_id' => $categorie->id,
            'duree_minutes' => $validated['duree_minutes'] ?? null,
            'date_examen' => $validated['date_examen'] ?? null, // ✅ nouveau
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
            'description' => ['nullable', 'string'],
            'duree_minutes' => ['nullable', 'integer', 'min:1'],
            'date_examen' => [
                'nullable',
                'date',
                'after_or_equal:today',
            ],
            'status' => [
                'required',
                'in:brouillon,publie,archive',
            ],
        ], [
            'titre.required' => 'Le titre est obligatoire.',
            'titre.unique' =>
                'Un examen avec ce titre existe déjà dans cette catégorie.',
            'duree_minutes.integer' =>
                'La durée doit être un nombre entier.',
            'date_examen.date' =>
                'La date doit être une date valide.',
            'date_examen.after_or_equal' =>
                "La date de l'examen ne peut pas être dans le passé.",
        ]);

        if (
            $validated['status'] === 'publie' &&
            $examen->status !== 'archive'
        ) {
            return back()->withErrors([
                'status' =>
                    'Vous ne pouvez publier un examen que s\'il est finalisé (archivé). Veuillez d\'abord terminer sa création.',
            ])->withInput();
        }

        $examen->update([
            'titre' => $validated['titre'],
            'description' => $validated['description'] ?? null,
            'duree_minutes' => $validated['duree_minutes'] ?? null,
            'date_examen' => $validated['date_examen'] ?? null,
            'status' => $validated['status'],
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
