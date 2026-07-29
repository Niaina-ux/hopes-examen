<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\Examen;
use App\Models\GlisserDeposer;
use App\Models\GlisserDeposerItem;
use App\Models\GlisserDeposerQuestion;
use App\Models\GlisserDeposerZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfExamenGlisserDeposerQuestionController extends Controller
{
    public function index(string $slug, Examen $examen, GlisserDeposer $glisserdeposer)
    {
        $questions = $glisserdeposer->questions()->withCount(['zones', 'items'])->get();

        return view('prof.examen.glisserdeposer.question.index', compact('slug', 'examen', 'glisserdeposer', 'questions'));
    }

    public function create(string $slug, Examen $examen, GlisserDeposer $glisserdeposer)
    {
        return view('prof.examen.glisserdeposer.questions.create', compact('slug', 'examen', 'glisserdeposer'));
    }

    public function store(Request $request, string $slug, Examen $examen, GlisserDeposer $glisserdeposer)
    {
        $validated = $request->validate([
            'enonce'  => ['required', 'string'],
            'image'   => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'points'  => ['required', 'numeric', 'min:0.1'],
            'zones'   => ['required', 'array', 'min:1'],
            'zones.*.nom_zone'    => ['nullable', 'string'],
            'zones.*.position_x'  => ['required', 'numeric', 'min:0', 'max:100'],
            'zones.*.position_y'  => ['required', 'numeric', 'min:0', 'max:100'],
            'zones.*.texte'       => ['required', 'string'], // le mot/item correct pour cette zone
        ], [
            'image.required' => 'L\'image du schéma est obligatoire.',
            'zones.required' => 'Ajoutez au moins une zone à placer.',
        ]);

        $textes = collect($validated['zones'])
            ->pluck('texte')
            ->map(fn ($texte) => trim($texte))
            ->filter();

        if ($textes->count() !== $textes->unique()->count()) {
            return back()
                ->withErrors([
                    'zones' => 'Les textes des zones doivent être uniques. Deux zones ne peuvent pas avoir le même texte.',
                ])
                ->withInput();
        }

        [$imageName, $largeur, $hauteur] = $this->uploadImage($request->file('image'));

        DB::transaction(function () use ($validated, $glisserdeposer, $imageName, $largeur, $hauteur) {
            $question = GlisserDeposerQuestion::create([
                'glisser_deposer_id' => $glisserdeposer->id,
                'enonce'             => $validated['enonce'] ?? null,
                'image'              => $imageName,
                'image_largeur'      => $largeur,
                'image_hauteur'      => $hauteur,
                'points'             => $validated['points'],
                'ordre'              => ($glisserdeposer->questions()->max('ordre') ?? 0) + 1,
            ]);

            foreach ($validated['zones'] as $index => $zoneData) {
                $zone = GlisserDeposerZone::create([
                    'glisser_deposer_question_id' => $question->id,
                    'nom_zone'                    => $zoneData['nom_zone'] ?? ('Zone ' . ($index + 1)),
                    'position_x'                  => $zoneData['position_x'],
                    'position_y'                  => $zoneData['position_y'],
                    'ordre'                       => $index + 1,
                ]);

                GlisserDeposerItem::create([
                    'glisser_deposer_question_id' => $question->id,
                    'glisser_deposer_zone_id'     => $zone->id,
                    'texte'                       => $zoneData['texte'],
                    'ordre'                       => $index + 1,
                ]);
            }
        });

        return redirect()
            ->route('prof.examen.glisserdeposer', [$slug, $examen->id])
            ->with('success', 'Question ajoutée avec succès.');
    }

    public function edit(string $slug, Examen $examen, GlisserDeposer $glisserdeposer, GlisserDeposerQuestion $question)
    {
        $question->load('zones.item');

        $zonesExistantes = $question->zones->map(function ($zone) {
            return [
                'x' => (float) $zone->position_x,
                'y' => (float) $zone->position_y,
                'nom_zone' => $zone->nom_zone,
                'texte' => optional($zone->item)->texte,
            ];
        });

        return view(
            'prof.examen.glisserdeposer.questions.edit',
            compact(
                'slug',
                'examen',
                'glisserdeposer',
                'question',
                'zonesExistantes'
            )
        );
    }

    public function update(Request $request, string $slug, Examen $examen, GlisserDeposer $glisserdeposer, GlisserDeposerQuestion $question)
    {
        $validated = $request->validate([
            'enonce'  => ['required', 'string'],
            'image'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'points'  => ['required', 'numeric', 'min:0.1'],
            'zones'   => ['required', 'array', 'min:1'],
            'zones.*.nom_zone'    => ['nullable', 'string'],
            'zones.*.position_x'  => ['required', 'numeric', 'min:0', 'max:100'],
            'zones.*.position_y'  => ['required', 'numeric', 'min:0', 'max:100'],
            'zones.*.texte'       => ['required', 'string'],
        ]);

        $textes = collect($validated['zones'])
            ->pluck('texte')
            ->map(fn ($texte) => trim($texte))
            ->filter();

        if ($textes->count() !== $textes->unique()->count()) {
            return back()
                ->withErrors([
                    'zones' => 'Les textes des zones doivent être uniques. Deux zones ne peuvent pas avoir le même texte.',
                ])
                ->withInput();
        }

        $imageName = $question->image;
        $largeur = $question->image_largeur;
        $hauteur = $question->image_hauteur;

        if ($request->hasFile('image')) {
            if ($question->image && file_exists(public_path('images/glisserdeposer/' . $question->image))) {
                unlink(public_path('images/glisserdeposer/' . $question->image));
            }
            [$imageName, $largeur, $hauteur] = $this->uploadImage($request->file('image'));
        }

        DB::transaction(function () use ($validated, $question, $imageName, $largeur, $hauteur) {
            $question->update([
                'enonce'        => $validated['enonce'] ?? null,
                'image'         => $imageName,
                'image_largeur' => $largeur,
                'image_hauteur' => $hauteur,
                'points'        => $validated['points'],
            ]);

            $question->zones()->delete();

            foreach ($validated['zones'] as $index => $zoneData) {
                $zone = GlisserDeposerZone::create([
                    'glisser_deposer_question_id' => $question->id,
                    'nom_zone'                    => $zoneData['nom_zone'] ?? ('Zone ' . ($index + 1)),
                    'position_x'                  => $zoneData['position_x'],
                    'position_y'                  => $zoneData['position_y'],
                    'ordre'                       => $index + 1,
                ]);

                GlisserDeposerItem::create([
                    'glisser_deposer_question_id' => $question->id,
                    'glisser_deposer_zone_id'     => $zone->id,
                    'texte'                       => $zoneData['texte'],
                    'ordre'                       => $index + 1,
                ]);
            }
        });

        return redirect()
            ->route('prof.examen.glisserdeposer', [$slug, $examen->id, $glisserdeposer->id])
            ->with('success', 'Question modifiée avec succès.');
    }

    public function destroy(string $slug, Examen $examen, GlisserDeposer $glisserdeposer, GlisserDeposerQuestion $question)
    {
        if ($question->image && file_exists(public_path('images/glisserdeposer/' . $question->image))) {
            unlink(public_path('images/glisserdeposer/' . $question->image));
        }

        $question->delete();

        return redirect()
            ->back()
            ->with('success', 'Question supprimée avec succès.');
    }

    /**
     * Mandefa ny sary any amin'ny public/images/glisserdeposer, ary mamerina
     * [nom_fichier, largeur, hauteur]
     */
    private function uploadImage($file): array
    {
        $imageName = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('images/glisserdeposer'), $imageName);

        [$largeur, $hauteur] = getimagesize(public_path('images/glisserdeposer/' . $imageName));

        return [$imageName, $largeur, $hauteur];
    }
}
