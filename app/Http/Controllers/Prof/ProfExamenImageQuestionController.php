<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\Examen;
use App\Models\ImageExercice;
use App\Models\ImageExerciceQuestion;
use Illuminate\Http\Request;

class ProfExamenImageQuestionController extends Controller
{

    public function create(string $slug, Examen $examen, ImageExercice $image)
    {
        return view('prof.examen.image-exercice.questions.create', compact('slug', 'examen', 'image'));
    }

    public function store(Request $request, string $slug, Examen $examen, ImageExercice $image)
    {
        $validated = $request->validate([
            'instruction' => ['required', 'string'],
            'image'       => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'points'      => [
                'required',
                'numeric',
                'min:0.1',
                function ($attribute, $value, $fail) use ($image) {
                    $pointsExistants = $image->questions()->sum('points');
                    $nouveauTotal = $pointsExistants + $value;

                    if ($nouveauTotal > $image->note_totale) {
                        $restant = $image->note_totale - $pointsExistants;
                        $fail("Les points dépassent le total autorisé ({$image->note_totale} pts). Il reste {$restant} pt(s) disponible(s).");
                    }
                },
            ],
        ], [
            'instruction.required' => 'L\'instruction est obligatoire.',
            'image.required'       => 'L\'image est obligatoire.',
        ]);

        $file = $request->file('image');
        $imageName = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('images/image_exercice'), $imageName);

        ImageExerciceQuestion::create([
            'image_exercice_id' => $image->id,
            'instruction'       => $validated['instruction'],
            'image'             => $imageName,
            'points'            => $validated['points'],
            'ordre'             => ($image->questions()->max('ordre') ?? 0) + 1,
        ]);

        return redirect()
            ->route('prof.examen.image', [$slug, $examen->id])
            ->with('success', 'Image ajoutée avec succès.');
    }

    public function edit(string $slug, Examen $examen, ImageExercice $image, ImageExerciceQuestion $question)
    {
        return view('prof.examen.image-exercice.questions.edit', compact('slug', 'examen', 'image', 'question'));
    }

    public function update(Request $request, string $slug, Examen $examen, ImageExercice $image, ImageExerciceQuestion $question)
    {
        $validated = $request->validate([
            'instruction' => ['required', 'string'],
            'image'       => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'points'      => [
                'required',
                'numeric',
                'min:0.1',
                function ($attribute, $value, $fail) use ($image) {
                    $pointsExistants = $image->questions()->sum('points');
                    $nouveauTotal = $pointsExistants + $value;

                    if ($nouveauTotal > $image->note_totale) {
                        $restant = $image->note_totale - $pointsExistants;
                        $fail("Les points dépassent le total autorisé ({$image->note_totale} pts). Il reste {$restant} pt(s) disponible(s).");
                    }
                },
            ],
        ], [
            'instruction.required' => 'L\'instruction est obligatoire.',
            'image.required'       => 'L\'image est obligatoire.',
        ]);

        $imageName = $question->image;

        if ($request->hasFile('image')) {
            if ($question->image && file_exists(public_path('images/image_exercice/' . $question->image))) {
                unlink(public_path('images/image_exercice/' . $question->image));
            }
            $file = $request->file('image');
            $imageName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/image_exercice'), $imageName);
        }

        $question->update([
            'instruction' => $validated['instruction'],
            'image'       => $imageName,
            'points'      => $validated['points'],
        ]);

        return redirect()
            ->route('prof.examen.image', [$slug, $examen->id])
            ->with('success', 'Image modifiée avec succès.');
    }

    public function destroy(string $slug, Examen $examen, ImageExercice $image, ImageExerciceQuestion $question)
    {
        if ($question->image && file_exists(public_path('images/exercice/' . $question->image))) {
            unlink(public_path('images/exercice/' . $question->image));
        }

        $question->delete();

        return redirect()
            ->back()
            ->with('success', 'Image supprimée avec succès.');
    }
}
