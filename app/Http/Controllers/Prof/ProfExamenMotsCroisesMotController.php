<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Examen;
use App\Models\MotsCroises;
use App\Models\MotsCroisesMot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfExamenMotsCroisesMotController extends Controller
{
    public function show(string $slug, Examen $examen, MotsCroises $motsCroise)
    {
        $mots = $motsCroise->motsCroisesMots()->orderBy('numero')->get();

        // ✅ Mikajy ny dimension an'ny grille
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

        // ✅ Mamorona ny "grille" ho an'ny preview
        $grille = [];
        for ($y = 0; $y < $hauteur; $y++) {
            for ($x = 0; $x < $largeur; $x++) {
                $grille[$y][$x] = [
                    'lettre'         => null,
                    'numero'         => null,
                    'lettre_visible' => false,
                ];
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
                    $grille[$y][$x]['numero'] = $mot->numero;
                }

                if (in_array($i, $positionsVisibles)) {
                    $grille[$y][$x]['lettre_visible'] = true;
                }
            }
        }

        return view('prof.examen.motscroises.mots.show', compact(
            'slug', 'examen', 'motsCroise', 'mots', 'grille', 'largeur', 'hauteur'
        ));
    }

    public function create(string $slug, Examen $examen, MotsCroises $motsCroise)
    {
        $dernierNumero = $motsCroise->motsCroisesMots()->max('numero') ?? 0;

        return view('prof.examen.motscroises.mots.create', compact('slug', 'examen', 'motsCroise', 'dernierNumero'));
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
            'mots.*.indice'                       => ['required', 'string'],
            'mots.*.reponse'                      => ['required', 'string'],
            'mots.*.direction'                    => ['required', 'in:horizontal,vertical'],
            'mots.*.position_x'                   => ['required', 'integer', 'min:0'],
            'mots.*.position_y'                   => ['required', 'integer', 'min:0'],
            'mots.*.numero'                        => ['required', 'integer', 'min:1'],
            'mots.*.points'                        => ['required', 'numeric', 'min:0.1'],
            'mots.*.positions_lettres_visibles'    => ['nullable', 'array'],
            'mots.*.positions_lettres_visibles.*'  => ['integer', 'min:0'],
        ], [
            'titre.required' => 'Le titre est obligatoire.',
            'mots.required'  => 'Ajoutez au moins un mot dans la grille.',
            'mots.min'       => 'Ajoutez au moins un mot dans la grille.',
        ]);

        // ✅ Fanamarinana ny "intersections" eo amin'ny mots rehetra alefa
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
            ->route('prof.examen.motscroises.show', [$slug, $examen->id])
            ->with('success', 'Exercice mots croisés créé avec succès.');
    }

    /**
     * Manamarina ny intersections eo amin'ny lisitry mots rehetra (talohan'ny tehirizina)
     */
    private function verifierIntersectionsListe(array $mots): ?string
    {
        $cases = []; // "x_y" => lettre

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

    public function edit(string $slug, Examen $examen, MotsCroises $motsCroise, MotsCroisesMot $mot)
    {
        return view('prof.examen.motscroises.mot.edit', compact('slug', 'examen', 'motsCroise', 'mot'));
    }

    public function update(Request $request, string $slug, Examen $examen, MotsCroises $motsCroise, MotsCroisesMot $mot)
    {
        $validated = $request->validate([
            'indice'                       => ['required', 'string'],
            'reponse'                      => ['required', 'string', 'max:30'],
            'direction'                    => ['required', 'in:horizontal,vertical'],
            'position_x'                   => ['required', 'integer', 'min:0'],
            'position_y'                   => ['required', 'integer', 'min:0'],
            'numero'                       => ['required', 'integer', 'min:1'],
            'points'                       => ['required', 'numeric', 'min:0.1'],
            'positions_lettres_visibles'   => ['nullable', 'array'],
            'positions_lettres_visibles.*' => ['integer', 'min:0'],
        ], [
            'indice.required'  => 'L\'indice est obligatoire.',
            'reponse.required' => 'La réponse est obligatoire.',
        ]);

        $reponse = strtoupper(trim($validated['reponse']));

        // ✅ Fanamarinana ny intersections, tsy anisan'ny "mot" ankehitriny
        $erreurIntersection = $this->verifierIntersections(
            $motsCroise,
            $reponse,
            $validated['direction'],
            $validated['position_x'],
            $validated['position_y'],
            $mot->id // ignore ilay mot ankehitriny
        );

        if ($erreurIntersection) {
            return back()->withInput()->withErrors(['reponse' => $erreurIntersection]);
        }

        $mot->update([
            'indice'                      => $validated['indice'],
            'reponse'                     => $reponse,
            'direction'                   => $validated['direction'],
            'position_x'                  => $validated['position_x'],
            'position_y'                  => $validated['position_y'],
            'numero'                      => $validated['numero'],
            'points'                      => $validated['points'],
            'positions_lettres_visibles'  => $validated['positions_lettres_visibles'] ?? [],
        ]);

        return redirect()
            ->route('prof.examen.motscroises.mot.index', [$slug, $examen->id, $motsCroise->id])
            ->with('success', 'Mot modifié avec succès.');
    }

    public function destroy(string $slug, Examen $examen, MotsCroises $motsCroise, MotsCroisesMot $mot)
    {
        $mot->delete();

        return redirect()
            ->back()
            ->with('success', 'Mot supprimé avec succès.');
    }

    /**
     * Manamarina raha mifanaraka ny litera eo amin'ny "case" iraisana (intersection)
     * amin'ny teny vaovao/voaova sy ny teny efa misy ao amin'ity mots_croises ity.
     *
     * @return string|null Message d'erreur si conflit, sinon null
     */
    private function verifierIntersections(
        MotsCroises $motsCroise,
        string $reponse,
        string $direction,
        int $positionX,
        int $positionY,
        ?int $ignoreMotId = null
    ): ?string {
        $motsExistants = $motsCroise->motsCroisesMots()
            ->when($ignoreMotId, fn($q) => $q->where('id', '!=', $ignoreMotId))
            ->get();

        // Mamorona ny "cases" an'ny mot vaovao/voaova: [x_y => lettre]
        $casesNouveauMot = [];
        for ($i = 0; $i < strlen($reponse); $i++) {
            $x = $direction === 'horizontal' ? $positionX + $i : $positionX;
            $y = $direction === 'horizontal' ? $positionY : $positionY + $i;
            $casesNouveauMot["{$x}_{$y}"] = $reponse[$i];
        }

        // Mampitaha amin'ny "cases" an'ny mot efa misy tsirairay
        foreach ($motsExistants as $motExistant) {
            $reponseExistante = strtoupper($motExistant->reponse);

            for ($i = 0; $i < strlen($reponseExistante); $i++) {
                $x = $motExistant->direction === 'horizontal' ? $motExistant->position_x + $i : $motExistant->position_x;
                $y = $motExistant->direction === 'horizontal' ? $motExistant->position_y : $motExistant->position_y + $i;
                $key = "{$x}_{$y}";

                if (isset($casesNouveauMot[$key]) && $casesNouveauMot[$key] !== $reponseExistante[$i]) {
                    return "Conflit à la position (x={$x}, y={$y}) : la lettre '{$casesNouveauMot[$key]}' ne correspond pas à la lettre '{$reponseExistante[$i]}' du mot « {$motExistant->reponse} ».";
                }
            }
        }

        return null;
    }
}
