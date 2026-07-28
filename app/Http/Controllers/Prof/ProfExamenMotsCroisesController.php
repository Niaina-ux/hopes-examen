<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Examen;
use App\Models\MotsCroises;
use App\Models\MotsCroisesMot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfExamenMotsCroisesController extends Controller
{
    public function show(string $slug, Examen $examen)
    {
        $categorie = Categorie::where('slug', $slug)->firstOrFail();

        $motscroises = MotsCroises::where('examen_id', $examen->id)
            ->where('categorie_id', $categorie->id)
            ->withCount('motsCroisesMots')
            ->orderBy('ordre')
            ->get();

        // ✅ Mikajy ny "grille preview" (kely) isaky ny motscroise, ho an'ny fisehoana
        $apercus = [];

        foreach ($motscroises as $motCroise) {
        $mots = $motCroise->motsCroisesMots;

        $largeur = 0;
        $hauteur = 0;

        foreach ($mots as $mot) {
            if ($mot->direction === 'horizontal') {
                $largeur = max($largeur, $mot->position_x + strlen($mot->reponse));
                $hauteur = max($hauteur, $mot->position_y + 1);
            } else {
                $largeur = max($largeur, $mot->position_x + 1);
                $hauteur = max($hauteur, $mot->position_y + strlen($mot->reponse));
            }
        }

        $grille = [];
        for ($y = 0; $y < $hauteur; $y++) {
            for ($x = 0; $x < $largeur; $x++) {
                $grille[$y][$x] = ['lettre' => null, 'lettre_visible' => false, 'numero' => null]; // ✅ ampio 'numero'
            }
        }

        foreach ($mots as $mot) {
            $longueur = strlen($mot->reponse);
            $positionsVisibles = $mot->positions_lettres_visibles ?? [];

            for ($i = 0; $i < $longueur; $i++) {
                $x = $mot->direction === 'horizontal' ? $mot->position_x + $i : $mot->position_x;
                $y = $mot->direction === 'horizontal' ? $mot->position_y : $mot->position_y + $i;

                $grille[$y][$x]['lettre'] = $mot->reponse[$i];

                if ($i === 0) {
                    $grille[$y][$x]['numero'] = $mot->numero; // ✅ ampio
                }

                if (in_array($i, $positionsVisibles)) {
                    $grille[$y][$x]['lettre_visible'] = true;
                }
            }
        }

        $apercus[$motCroise->id] = [
            'grille'  => $grille,
            'largeur' => $largeur,
            'hauteur' => $hauteur,
        ];
    }

        return view('prof.examen.motscroises.show', compact('slug', 'examen', 'motscroises', 'apercus'));
    }

    public function create(string $slug, Examen $examen)
    {
        $categorie = Categorie::where('slug', $slug)->firstOrFail();

        return view('prof.examen.motscroises.create', compact('slug', 'examen', 'categorie'));
    }

    
    public function store(Request $request, string $slug, Examen $examen)
    {
        $categorie = Categorie::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'titre'         => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'duree_minutes' => ['nullable', 'integer', 'min:1'],
            'note_totale'   => ['nullable', 'numeric', 'min:0.1'],
            'largeur'       => ['required', 'integer', 'min:1'],
            'hauteur'       => ['required', 'integer', 'min:1'],
            'mots'          => ['required', 'array', 'min:1'],
            'mots.*.indice'                        => ['required', 'string'],
            'mots.*.reponse'                       => ['required', 'string'],
            'mots.*.direction'                     => ['required', 'in:horizontal,vertical'],
            'mots.*.position_x'                    => ['required', 'integer', 'min:0'],
            'mots.*.position_y'                    => ['required', 'integer', 'min:0'],
            'mots.*.numero'                        => ['required', 'integer', 'min:1'],
            'mots.*.points'                        => ['required', 'numeric', 'min:0.1'],
            'mots.*.positions_lettres_visibles'    => ['nullable', 'array'],
            'mots.*.positions_lettres_visibles.*'  => ['integer', 'min:0'],
        ], [
            'titre.required' => 'Le titre est obligatoire.',
            'mots.required'  => 'Ajoutez au moins un mot dans la grille.',
            'mots.min'       => 'Ajoutez au moins un mot dans la grille.',
        ]);

        // ✅ Fanamarinana ny "intersections"
        $erreurIntersection = $this->verifierIntersectionsListe($validated['mots']);
        if ($erreurIntersection) {
            return back()->withInput()->withErrors(['mots' => $erreurIntersection]);
        }

        // ✅ Fanamarinana ny note_totale
        if (!empty($validated['note_totale'])) {
            $totalPoints = array_sum(array_column($validated['mots'], 'points'));
            if ($totalPoints > $validated['note_totale']) {
                return back()->withInput()->withErrors([
                    'note_totale' => "Le total des points des mots ({$totalPoints}) dépasse la note totale ({$validated['note_totale']}).",
                ]);
            }
        }

        DB::transaction(function () use ($validated, $examen, $categorie) {
            $motCroise = MotsCroises::create([
                'examen_id'     => $examen->id,
                'categorie_id'  => $categorie->id,
                'titre'         => $validated['titre'],
                'description'   => $validated['description'] ?? null,
                'duree_minutes' => $validated['duree_minutes'] ?? null,
                'note_totale'   => $validated['note_totale'] ?? null,
                'ordre'         => (MotsCroises::where('examen_id', $examen->id)->max('ordre') ?? 0) + 1,
            ]);

            foreach ($validated['mots'] as $mot) {
                MotsCroisesMot::create([
                    'mots_croises_id'            => $motCroise->id,
                    'indice'                     => $mot['indice'],
                    'reponse'                    => strtoupper($mot['reponse']),
                    'direction'                  => $mot['direction'],
                    'position_x'                 => $mot['position_x'],
                    'position_y'                 => $mot['position_y'],
                    'numero'                     => $mot['numero'],
                    'points'                     => $mot['points'],
                    'positions_lettres_visibles' => $mot['positions_lettres_visibles'] ?? [],
                ]);
            }
        });

        return redirect()
            ->route('prof.examen.motscroises', [$slug, $examen->id]) // ✅ mifanaraka amin'ny route anarana
            ->with('success', 'Exercice mots croisés créé avec succès.');
    }

    public function edit(string $slug, Examen $examen, MotsCroises $motscroises) // ✅ anarana parameter "motscroises"
    {
        $mots = $motscroises->motsCroisesMots()->orderBy('numero')->get();

        $largeur = 10;
        $hauteur = 10;
        foreach ($mots as $mot) {
            if ($mot->direction === 'horizontal') {
                $largeur = max($largeur, $mot->position_x + strlen($mot->reponse));
                $hauteur = max($hauteur, $mot->position_y + 1);
            } else {
                $largeur = max($largeur, $mot->position_x + 1);
                $hauteur = max($hauteur, $mot->position_y + strlen($mot->reponse));
            }
        }

        return view('prof.examen.motscroises.edit', compact('slug', 'examen', 'motscroises', 'mots', 'largeur', 'hauteur'));
    }

    public function update(Request $request, string $slug, Examen $examen, MotsCroises $motscroises)
    {
        $validated = $request->validate([
            'titre'         => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'duree_minutes' => ['nullable', 'integer', 'min:1'],
            'note_totale'   => ['nullable', 'numeric', 'min:0.1'],
            'largeur'       => ['required', 'integer', 'min:1'],
            'hauteur'       => ['required', 'integer', 'min:1'],
            'mots'          => ['required', 'array', 'min:1'],
            'mots.*.indice'                        => ['required', 'string'],
            'mots.*.reponse'                       => ['required', 'string'],
            'mots.*.direction'                     => ['required', 'in:horizontal,vertical'],
            'mots.*.position_x'                    => ['required', 'integer', 'min:0'],
            'mots.*.position_y'                    => ['required', 'integer', 'min:0'],
            'mots.*.numero'                         => ['required', 'integer', 'min:1'],
            'mots.*.points'                         => ['required', 'numeric', 'min:0.1'],
            'mots.*.positions_lettres_visibles'     => ['nullable', 'array'],
            'mots.*.positions_lettres_visibles.*'   => ['integer', 'min:0'],
        ], [
            'titre.required' => 'Le titre est obligatoire.',
            'mots.required'  => 'Ajoutez au moins un mot dans la grille.',
            'mots.min'       => 'Ajoutez au moins un mot dans la grille.',
        ]);

        $erreurIntersection = $this->verifierIntersectionsListe($validated['mots']);
        if ($erreurIntersection) {
            return back()->withInput()->withErrors(['mots' => $erreurIntersection]);
        }

        if (!empty($validated['note_totale'])) {
            $totalPoints = array_sum(array_column($validated['mots'], 'points'));
            if ($totalPoints > $validated['note_totale']) {
                return back()->withInput()->withErrors([
                    'note_totale' => "Le total des points des mots ({$totalPoints}) dépasse la note totale ({$validated['note_totale']}).",
                ]);
            }
        }

        DB::transaction(function () use ($validated, $motscroises) {
            $motscroises->update([
                'titre'         => $validated['titre'],
                'description'   => $validated['description'] ?? null,
                'duree_minutes' => $validated['duree_minutes'] ?? null,
                'note_totale'   => $validated['note_totale'] ?? null,
            ]);

            $motscroises->motsCroisesMots()->delete();

            foreach ($validated['mots'] as $mot) {
                MotsCroisesMot::create([
                    'mots_croises_id'            => $motscroises->id,
                    'indice'                     => $mot['indice'],
                    'reponse'                    => strtoupper($mot['reponse']),
                    'direction'                  => $mot['direction'],
                    'position_x'                 => $mot['position_x'],
                    'position_y'                 => $mot['position_y'],
                    'numero'                     => $mot['numero'],
                    'points'                     => $mot['points'],
                    'positions_lettres_visibles' => $mot['positions_lettres_visibles'] ?? [],
                ]);
            }
        });

        return redirect()
            ->route('prof.examen.motscroises', [$slug, $examen->id])
            ->with('success', 'Exercice modifié avec succès.');
    }

    public function destroy(string $slug, Examen $examen, MotsCroises $motscroises)
    {
        $motscroises->delete();

        return redirect()
            ->back()
            ->with('success', 'Exercice supprimé avec succès.');
    }

    private function verifierIntersectionsListe(array $mots): ?string
    {
        $cases = [];

        foreach ($mots as $mot) {
            $reponse = strtoupper($mot['reponse']);

            for ($i = 0; $i < strlen($reponse); $i++) {
                $x = $mot['direction'] === 'horizontal' ? $mot['position_x'] + $i : $mot['position_x'];
                $y = $mot['direction'] === 'horizontal' ? $mot['position_y'] : $mot['position_y'] + $i;
                $key = "{$x}_{$y}";

                if (isset($cases[$key]) && $cases[$key] !== $reponse[$i]) {
                    return "Conflit à la position (x={$x}, y={$y}) : lettres différentes entre deux mots qui se croisent.";
                }

                $cases[$key] = $reponse[$i];
            }
        }

        return null;
    }
}
