<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\Examen;
use App\Models\Pointiller;
use App\Models\PointillerQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProfExamenPointillerQuestionController extends Controller
{
    public function show(string $slug, Examen $examen, Pointiller $pointiller)
    {
        $questions = $pointiller->pointillerQuestions()
            ->with('reponses.choices')
            ->orderBy('id', 'desc')
            ->get();
            
        return view('prof.examen.pointiller.quesitons.show', compact('slug','examen','pointiller','questions'));
    }

    public function create(string $slug, Examen $examen, Pointiller $pointiller)
    {
        return view('prof.examen.pointiller.quesitons.create', compact('slug', 'examen', 'pointiller'));
    }

    public function store(Request $request, string $slug, Examen $examen, Pointiller $pointiller)
    {
        $validated = $request->validate([
            'enonce' => [
                'required',
                'string',
                Rule::unique('pointiller_questions', 'enonce')
                    ->where('pointiller_id', $pointiller->id),
            ],
            'points' => ['required', 'numeric', 'min:0.1'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'video' => ['nullable', 'file', 'mimes:mp4,mov,avi,webm', 'max:20480'],
            'trous' => ['required', 'array', 'min:1'],
            'trous.*.reponse_correcte' => ['required', 'string'],
            'trous.*.choices' => ['required', 'array', 'min:2'],
            'trous.*.choices.*' => ['required', 'string'],
        ], [
            'enonce.required' => 'L\'énoncé est obligatoire.',
            'enonce.unique' => 'Cette question existe déjà dans cet exercice.',
            'trous.required' => 'Ajoutez au moins un trou avec sa réponse.',
            'trous.*.reponse_correcte.required' => 'Indiquez la réponse correcte pour chaque trou.',
            'trous.*.choices.min' => 'Chaque trou doit avoir au moins 2 choix dans la banque.',
        ]);

        DB::transaction(function () use ($request, $validated, $pointiller) {
            $imagePath = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $imageName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('images/questions'), $imageName);
                $imagePath = $imageName;
            }

            $videoPath = null;
            if ($request->hasFile('video')) {
                $file = $request->file('video');
                $videoName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('videos/questions'), $videoName);
                $videoPath = $videoName;
            }

            $question = PointillerQuestion::create([
                'pointiller_id' => $pointiller->id,
                'enonce' => $validated['enonce'],
                'image' => $imagePath,
                'video' => $videoPath,
                'points' => $validated['points'],
                'ordre' => $pointiller->pointillerQuestions()->count(),
            ]);

            foreach ($validated['trous'] as $position => $trou) {
                $reponse = $question->reponses()->create([
                    'position' => $position + 1,
                    'reponse_correcte' => trim($trou['reponse_correcte']),
                ]);

                $choices = $trou['choices'];
                if (!in_array(trim($trou['reponse_correcte']), array_map('trim', $choices))) {
                    $choices[] = $trou['reponse_correcte'];
                }

                foreach ($choices as $choiceText) {
                    if (empty(trim($choiceText))) continue;

                    $reponse->choices()->create([
                        'texte' => trim($choiceText),
                    ]);
                }
            }
        });

        return redirect()
            ->back()
            ->with('success', 'Question ajoutée avec succès.');
    }


    public function edit(string $slug, Examen $examen, Pointiller $pointiller, PointillerQuestion $question)
    {
        $question->load('reponses.choices');

        return view('prof.examen.pointiller.quesitons.edit', compact('slug', 'examen', 'pointiller', 'question'));
    }

    public function update(Request $request, string $slug, Examen $examen, Pointiller $pointiller, PointillerQuestion $question)
    {
        $validated = $request->validate([
            'enonce' => [
                'required',
                'string',
                Rule::unique('pointiller_questions', 'enonce')
                    ->where('pointiller_id', $pointiller->id)
                    ->ignore($question->id),
            ],
            'points' => ['required', 'numeric', 'min:0.1'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'video' => ['nullable', 'file', 'mimes:mp4,mov,avi,webm', 'max:20480'],
            'trous' => ['required', 'array', 'min:1'],
            'trous.*.reponse_correcte' => ['required', 'string'],
            'trous.*.choices' => ['required', 'array', 'min:2'],
            'trous.*.choices.*' => ['required', 'string'],
        ], [
            'enonce.required' => 'L\'énoncé est obligatoire.',
            'enonce.unique' => 'Cette question existe déjà dans cet exercice.',
            'trous.required' => 'Ajoutez au moins un trou avec sa réponse.',
            'trous.*.reponse_correcte.required' => 'Indiquez la réponse correcte pour chaque trou.',
            'trous.*.choices.min' => 'Chaque trou doit avoir au moins 2 choix dans la banque.',
        ]);

        DB::transaction(function () use ($request, $validated, $question) {
            $imagePath = $question->image;
            if ($request->hasFile('image')) {
                if ($question->image && file_exists(public_path('images/questions/' . $question->image))) {
                    unlink(public_path('images/questions/' . $question->image));
                }
                $file = $request->file('image');
                $imageName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('images/questions'), $imageName);
                $imagePath = $imageName;
            }

            $videoPath = $question->video;
            if ($request->hasFile('video')) {
                if ($question->video && file_exists(public_path('videos/questions/' . $question->video))) {
                    unlink(public_path('videos/questions/' . $question->video));
                }
                $file = $request->file('video');
                $videoName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('videos/questions'), $videoName);
                $videoPath = $videoName;
            }

            $question->update([
                'enonce' => $validated['enonce'],
                'image' => $imagePath,
                'video' => $videoPath,
                'points' => $validated['points'],
            ]);

            $question->reponses()->delete();

            foreach ($validated['trous'] as $position => $trou) {
                $reponse = $question->reponses()->create([
                    'position' => $position + 1,
                    'reponse_correcte' => trim($trou['reponse_correcte']),
                ]);

                foreach ($trou['choices'] as $choiceText) {
                    if (empty(trim($choiceText))) continue;

                    $reponse->choices()->create([
                        'texte' => trim($choiceText),
                    ]);
                }
            }
        });

        return redirect()
            ->route('prof.examen.pointiller', [$slug, $examen->id])
            ->with('success', 'Question modifiée avec succès.');
    }

    public function destroy(string $slug, Examen $examen, Pointiller $pointiller, PointillerQuestion $question)
    {
        if ($question->image && file_exists(public_path('images/questions/' . $question->image))) {
            unlink(public_path('images/questions/' . $question->image));
        }

        if ($question->video && file_exists(public_path('videos/questions/' . $question->video))) {
            unlink(public_path('videos/questions/' . $question->video));
        }

        $question->delete();

        return redirect()
            ->route('prof.examen.pointiller.question.show', [$slug, $examen->id, $pointiller->id])
            ->with('success', 'Question supprimée avec succès.');
    }
}
