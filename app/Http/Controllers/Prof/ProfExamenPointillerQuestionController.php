<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Examen;
use App\Models\Pointiller;
use App\Models\PointillerQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProfExamenPointillerQuestionController extends Controller
{

    public function create(string $slug,  int $pointillerId)
    {
        $categorie = Categorie::where('slug', $slug)->firstOrFail();

        $pointiller = Pointiller::where('categorie_id', $categorie->id)->find($pointillerId);
        if (!$pointiller) {
            return redirect()
                ->route('prof.question.pointiller', $slug)
                ->with('error', "Ce exericice est introuvable pour cette catégorie.");
        }
        return view('prof.questions.pointiller.quesitons.create', compact('slug',  'pointiller'));
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

        $reponsesCorrectes = collect($validated['trous'])
            ->pluck('reponse_correcte')
            ->map(fn($reponse) => mb_strtolower(trim($reponse)))
            ->filter()
            ->values();

        if ($reponsesCorrectes->count() !== $reponsesCorrectes->unique()->count()) {
            return back()->withInput()->withErrors([
                'trous' => 'Les réponses correctes des trous ne doivent pas être identiques.',
            ]);
        }

        foreach ($validated['trous'] as $index => $trou) {
            $choices = collect($trou['choices'])
                ->map(fn($choice) => mb_strtolower(trim($choice)))
                ->filter()
                ->values();

            if ($choices->count() !== $choices->unique()->count()) {
                return back()->withInput()->withErrors([
                    "trous.$index.choices" => 'Les choix d’un même trou ne doivent pas être identiques.',
                ]);
            }
        }

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
                'ordre' => $pointiller->pointillerQuestions()->count() + 1,
            ]);

            foreach ($validated['trous'] as $position => $trou) {
                $reponseCorrecte = trim($trou['reponse_correcte']);

                $reponse = $question->reponses()->create([
                    'position' => $position + 1,
                    'reponse_correcte' => $reponseCorrecte,
                ]);

                $choices = collect($trou['choices'])
                    ->map(fn($choice) => trim($choice))
                    ->filter()
                    ->unique(fn($choice) => mb_strtolower($choice))
                    ->values();

                if (!$choices->contains(fn($choice) => mb_strtolower($choice) === mb_strtolower($reponseCorrecte))) {
                    $choices->push($reponseCorrecte);
                }

                foreach ($choices as $choiceText) {
                    $reponse->choices()->create([
                        'texte' => $choiceText,
                    ]);
                }
            }
        });

        return redirect()
            ->back()
            ->with('success', 'Question ajoutée avec succès.');
    }


    public function edit(string $slug, int $pointillerId, int $questionId)
    {
        $categorie = Categorie::where('slug', $slug)->firstOrFail();

        $pointiller = Pointiller::where('categorie_id', $categorie->id)
            ->find($pointillerId);

        if (!$pointiller) {
            return redirect()
                ->route('prof.question.pointiller', $slug)
                ->with('error', "Cet exercice est introuvable pour cette catégorie.");
        }

        $question = PointillerQuestion::where('pointiller_id', $pointiller->id)
            ->find($questionId);

        if (!$question) {
            return redirect()
                ->route('prof.question.pointiller', $slug)
                ->with('error', "Cette question est introuvable dans cet exercice.");
        }

        $question->load('reponses.choices');

        return view(
            'prof.questions.pointiller.quesitons.edit',
            compact('slug', 'pointiller', 'question')
        );
    }

    public function update(Request $request, string $slug, Pointiller $pointiller, PointillerQuestion $question)
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

        $reponsesCorrectes = collect($validated['trous'])
            ->pluck('reponse_correcte')
            ->map(fn($reponse) => mb_strtolower(trim($reponse)))
            ->filter()
            ->values();

        if ($reponsesCorrectes->count() !== $reponsesCorrectes->unique()->count()) {
            return back()->withInput()->withErrors([
                'trous' => 'Les réponses correctes des trous ne doivent pas être identiques.',
            ]);
        }

        foreach ($validated['trous'] as $index => $trou) {
            $choices = collect($trou['choices'])
                ->map(fn($choice) => mb_strtolower(trim($choice)))
                ->filter()
                ->values();

            if ($choices->count() !== $choices->unique()->count()) {
                return back()->withInput()->withErrors([
                    "trous.$index.choices" => 'Les choix d’un même trou ne doivent pas être identiques.',
                ]);
            }
        }

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
            ->route('prof.question.pointiller', $slug )
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
            ->back()
            ->with('success', 'Question supprimée avec succès.');
    }
}
