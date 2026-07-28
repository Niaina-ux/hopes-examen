<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\Examen;
use App\Models\Qcm;
use App\Models\QcmQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProfExamenQcmQuestionController extends Controller
{
    public function show(string $slug, Examen $examen, Qcm $qcm)
    {
        $questions = $qcm->qcmQuestions()->with('qcmChoices')->orderBy('id', 'desc')->get();

        return view('prof.examen.qcm.questions.show', compact('examen', 'qcm', 'questions', 'slug'));
    }

    public function create(string $slug, Examen $examen, Qcm $qcm)
    {
        return view('prof.examen.qcm.questions.create', compact('slug', 'examen', 'qcm'));
    }

    public function store(Request $request, string $slug, Examen $examen, Qcm $qcm)
    {
             
        $validated = $request->validate([
            'questions' => ['required', 'array', 'min:1'],
            'questions.*.enonce' => [
                'required',
                'string',
                Rule::unique('qcm_questions', 'enonce')->where('qcm_id', $qcm->id),
            ],
            'questions.*.points' => ['required', 'numeric', 'min:0.1'],
            'questions.*.duree_seconde' => ['required', 'integer', 'min:1'],
            'questions.*.reponse_type' => ['required', 'in:true_false,single,multiple'],
            'questions.*.image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'questions.*.video' => ['nullable', 'file', 'mimes:mp4,mov,avi,webm', 'max:20480'],
            'questions.*.choices' => ['nullable', 'array'],
            'questions.*.correct_choice' => ['nullable', 'integer'],
            'questions.*.choices.*.texte' => ['nullable', 'string'],
            'questions.*.choices.*.est_correcte' => ['nullable'],
            'questions.*.vrai_faux_correct' => ['nullable', 'in:vrai,faux'],
        ], [
            'questions.required' => 'Ajoutez au moins une question.',
            'questions.*.enonce.required' => 'L\'énoncé de chaque question est obligatoire.',
            'questions.*.video.mimes' => 'La vidéo doit être au format mp4, mov, avi ou webm.',
            'questions.*.video.max' => 'La vidéo ne doit pas dépasser 20 Mo.',
        ]);

        $pointsExistants = $qcm->qcmQuestions()->sum('points');
        $pointsNouveaux = collect($validated['questions'])->sum('points');
        $pointsTotal = $pointsExistants + $pointsNouveaux;

        if ($qcm->note_totale !== null && $pointsTotal > $qcm->note_totale) {
            return back()->withErrors([
                'questions' => "Le total des points ({$pointsTotal}) dépasse la note totale autorisée pour ce QCM ({$qcm->note_totale}). Points déjà utilisés : {$pointsExistants}.",
            ])->withInput();
        }

        // Fanamarinana manuel isaky ny question, araka ny reponse_type
        foreach ($validated['questions'] as $index => $q) {
            if ($q['reponse_type'] === 'true_false') {
                if (empty($q['vrai_faux_correct'])) {
                    return back()->withErrors([
                        "questions.$index.vrai_faux_correct" => 'Choisissez la bonne réponse (Vrai ou Faux).',
                    ])->withInput();
                }
            } elseif ($q['reponse_type'] === 'single') {
                if (!isset($q['correct_choice']) || $q['correct_choice'] === '') {
                    return back()->withErrors([
                        "questions.$index.correct_choice" => 'Sélectionnez la bonne réponse parmi les choix.',
                    ])->withInput();
                }
                if (empty($q['choices']) || count(array_filter($q['choices'], fn($c) => !empty($c['texte']))) < 2) {
                    return back()->withErrors([
                        "questions.$index.choices" => 'Ajoutez au moins 2 choix valides.',
                    ])->withInput();
                }
            } else {
                // multiple
                $hasCorrect = collect($q['choices'] ?? [])->contains(fn($c) => isset($c['est_correcte']));
                if (!$hasCorrect) {
                    return back()->withErrors([
                        "questions.$index.choices" => 'Sélectionnez au moins une bonne réponse parmi les choix.',
                    ])->withInput();
                }
                if (empty($q['choices']) || count(array_filter($q['choices'], fn($c) => !empty($c['texte']))) < 2) {
                    return back()->withErrors([
                        "questions.$index.choices" => 'Ajoutez au moins 2 choix valides.',
                    ])->withInput();
                }
            }
        }

        DB::transaction(function () use ($request, $validated, $qcm) {
            foreach ($validated['questions'] as $index => $questionData) {
                $imagePath = null;
                if ($request->hasFile("questions.$index.image")) {
                    $file = $request->file("questions.$index.image");
                    $imageName = time() . '_' . $index . '_' . $file->getClientOriginalName();
                    $file->move(public_path('images/questions'), $imageName);
                    $imagePath = $imageName;
                }

                $videoPath = null;
                if ($request->hasFile("questions.$index.video")) {
                    $file = $request->file("questions.$index.video");
                    $videoName = time() . '_' . $index . '_' . $file->getClientOriginalName();
                    $file->move(public_path('videos/questions'), $videoName);
                    $videoPath = $videoName;
                }

                $question = QcmQuestion::create([
                    'qcm_id' => $qcm->id,
                    'enonce' => $questionData['enonce'],
                    'image' => $imagePath,
                    'video' => $videoPath,
                    'reponse_type' => $questionData['reponse_type'],
                    'points' => $questionData['points'],
                    'duree_seconde'   => $questionData['duree_seconde'] ?? null,
                    'ordre' => $index,
                ]);

                if ($questionData['reponse_type'] === 'true_false') {
                    $question->qcmChoices()->createMany([
                        ['texte' => 'Vrai', 'est_correcte' => $questionData['vrai_faux_correct'] === 'vrai', 'ordre' => 0],
                        ['texte' => 'Faux', 'est_correcte' => $questionData['vrai_faux_correct'] === 'faux', 'ordre' => 1],
                    ]);
                } elseif ($questionData['reponse_type'] === 'single') {
                    $correctIndex = (int) ($questionData['correct_choice'] ?? -1);

                    foreach ($questionData['choices'] as $cIndex => $choice) {
                        if (empty($choice['texte'])) continue;
                        $question->qcmChoices()->create([
                            'texte' => $choice['texte'],
                            'est_correcte' => $cIndex === $correctIndex,
                            'ordre' => $cIndex,
                        ]);
                    }
                } else {
                    foreach ($questionData['choices'] as $cIndex => $choice) {
                        if (empty($choice['texte'])) continue;
                        $question->qcmChoices()->create([
                            'texte' => $choice['texte'],
                            'est_correcte' => isset($choice['est_correcte']),
                            'ordre' => $cIndex,
                        ]);
                    }
                }
            }
        });

        return redirect()
            ->back()
            ->with('success', 'Questions ajoutées avec succès.');
    }


    public function destroy(string $slug, Examen $examen, Qcm $qcm, QcmQuestion $question)
    {
        // Esory ny fichier image/video raha misy, alohan'ny hamafana ny record
        if ($question->image && file_exists(public_path('images/questions/' . $question->image))) {
            unlink(public_path('images/questions/' . $question->image));
        }

        if ($question->video && file_exists(public_path('videos/questions/' . $question->video))) {
            unlink(public_path('videos/questions/' . $question->video));
        }

        $question->delete();

        return redirect()
            ->route('prof.examen.qcm.question.show', [$slug, $examen->id, $qcm->id])
            ->with('success', 'Question supprimée avec succès.');
    }

    public function edit(string $slug, Examen $examen, Qcm $qcm, QcmQuestion $question)
    {
        $question->load('qcmChoices');

        return view('prof.examen.qcm.questions.edit', compact('slug', 'examen', 'qcm', 'question'));
    }

    public function update(Request $request, string $slug, Examen $examen, Qcm $qcm, QcmQuestion $question)
    {
        $validated = $request->validate([
            'enonce' => [
                'required',
                'string',
                'max:250',
                Rule::unique('qcm_questions', 'enonce')
                    ->where('qcm_id', $qcm->id)
                    ->ignore($question->id),
            ],
            'points' => ['required', 'numeric', 'min:0.1'],
            'duree_seconde' => ['required', 'integer', 'min:1'],
            'reponse_type' => ['required', 'in:true_false,single,multiple'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'video' => ['nullable', 'file', 'mimes:mp4,mov,avi,webm', 'max:20480'],
            'choices' => ['nullable', 'array'],
            'correct_choice' => ['nullable', 'integer'],
            'choices.*.texte' => ['nullable', 'string'],
            'choices.*.est_correcte' => ['nullable'],
            'vrai_faux_correct' => ['nullable', 'in:vrai,faux'],
        ], [
            'enonce.required' => 'L\'énoncé est obligatoire.',
            'enonce.unique' => 'Cette question existe déjà dans ce QCM.',
            'video.mimes' => 'La vidéo doit être au format mp4, mov, avi ou webm.',
            'video.max' => 'La vidéo ne doit pas dépasser 20 Mo.',
        ]);

        // Fanamarinana manuel araka ny reponse_type
        if ($validated['reponse_type'] === 'true_false') {
            if (empty($validated['vrai_faux_correct'])) {
                return back()->withErrors(['vrai_faux_correct' => 'Choisissez la bonne réponse (Vrai ou Faux).'])->withInput();
            }
        } elseif ($validated['reponse_type'] === 'single') {
            if (!isset($validated['correct_choice']) || $validated['correct_choice'] === '') {
                return back()->withErrors(['correct_choice' => 'Sélectionnez la bonne réponse parmi les choix.'])->withInput();
            }
            if (empty($validated['choices']) || count(array_filter($validated['choices'], fn($c) => !empty($c['texte']))) < 2) {
                return back()->withErrors(['choices' => 'Ajoutez au moins 2 choix valides.'])->withInput();
            }
        } else {
            $hasCorrect = collect($validated['choices'] ?? [])->contains(fn($c) => isset($c['est_correcte']));
            if (!$hasCorrect) {
                return back()->withErrors(['choices' => 'Sélectionnez au moins une bonne réponse parmi les choix.'])->withInput();
            }
            if (empty($validated['choices']) || count(array_filter($validated['choices'], fn($c) => !empty($c['texte']))) < 2) {
                return back()->withErrors(['choices' => 'Ajoutez au moins 2 choix valides.'])->withInput();
            }
        }

        // Fanamarinana ny note_totale (esorina ny points an'ity question ity amin'ny efa voarakitra, mba tsy hisy doublon)
        $pointsAutres = $qcm->qcmQuestions()->where('id', '!=', $question->id)->sum('points');
        $pointsTotal = $pointsAutres + $validated['points'];

        if ($qcm->note_totale !== null && $pointsTotal > $qcm->note_totale) {
            return back()->withErrors([
                'points' => "Le total des points ({$pointsTotal}) dépasse la note totale autorisée pour ce QCM ({$qcm->note_totale}).",
            ])->withInput();
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
                'reponse_type' => $validated['reponse_type'],
                'points' => $validated['points'],
                'duree_seconde'  => $validated['duree_seconde'] ?? null,
            ]);

            // Esorina daholo ny choix taloha, forohina indray araka ny angona vaovao
            $question->qcmChoices()->delete();

            if ($validated['reponse_type'] === 'true_false') {
                $question->qcmChoices()->createMany([
                    ['texte' => 'Vrai', 'est_correcte' => $validated['vrai_faux_correct'] === 'vrai', 'ordre' => 0],
                    ['texte' => 'Faux', 'est_correcte' => $validated['vrai_faux_correct'] === 'faux', 'ordre' => 1],
                ]);
            } elseif ($validated['reponse_type'] === 'single') {
                $correctIndex = (int) ($validated['correct_choice'] ?? -1);

                foreach ($validated['choices'] as $cIndex => $choice) {
                    if (empty($choice['texte'])) continue;
                    $question->qcmChoices()->create([
                        'texte' => $choice['texte'],
                        'est_correcte' => $cIndex === $correctIndex,
                        'ordre' => $cIndex,
                    ]);
                }
            } else {
                foreach ($validated['choices'] as $cIndex => $choice) {
                    if (empty($choice['texte'])) continue;
                    $question->qcmChoices()->create([
                        'texte' => $choice['texte'],
                        'est_correcte' => isset($choice['est_correcte']),
                        'ordre' => $cIndex,
                    ]);
                }
            }
        });

        return redirect()
            ->route('prof.examen.qcm.question.show', [$slug, $examen->id, $qcm->id])
            ->with('success', 'Question modifiée avec succès.');
    }
}