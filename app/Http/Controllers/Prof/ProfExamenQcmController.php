<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Examen;
use App\Models\Qcm;
use App\Models\QcmQuestion;
use Illuminate\Http\Request;

class ProfExamenQcmController extends Controller
{
    public function showbanque(string $slug)
    { 
        $categorie = Categorie::where('slug', $slug)->firstOrFail();
        $types = $categorie->typesExerciceAutorises;

        $qcms = Qcm::with('qcmQuestions')
            ->latest()
            ->get();

        return view('prof.questions.qcm.show', compact('types', 'slug', 'qcms'));
    }

    public function create(string $slug)
    {   
        return view('prof.questions.qcm.create', compact('slug'));
    }

    public function store(Request $request, string $slug, Examen $examen)
    {
        $categorie = Categorie::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ], [
            'titre.required' => 'Le titre est obligatoire.',
        ]);

        $qcm = Qcm::create([
            'categorie_id' => $categorie->id,
            'titre' => $validated['titre'],
            'description' => $validated['description'] ?? null,
            'ordre' => 0,
        ]);

        return redirect()
            ->route('prof.question.qcm', $slug)
            ->with('success', 'QCM créé avec succès. Vous pouvez maintenant ajouter des questions.');
    }


    public function edit(string $slug, int $qcmId)
    {
        $categorie = Categorie::where('slug', $slug)->firstOrFail();

        $qcm = Qcm::where('categorie_id', $categorie->id)->find($qcmId);

        if (!$qcm) {
            return redirect()
                ->route('prof.qcm.show', $slug)
                ->with('error', "Ce QCM est introuvable pour cette catégorie.");
        }

        return view('prof.questions.qcm.edit', compact('slug', 'qcm'));
    }

    public function update(Request $request, string $slug, int $qcmId)
    {
        $categorie = Categorie::where('slug', $slug)->firstOrFail();

        $qcm = Qcm::where('categorie_id', $categorie->id)->find($qcmId);

        if (!$qcm) {
            return redirect()
                ->route('prof.qcm.show', $slug)
                ->with('error', "Ce QCM est introuvable pour cette catégorie.");
        }

        $validated = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ], [
            'titre.required' => 'Le titre est obligatoire.',
        ]);

        $qcm->update([
            'titre' => $validated['titre'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('prof.question.qcm', $slug)
            ->with('success', 'QCM modifié avec succès.');
    }

    public function destroy(string $slug,  Examen $examen, Qcm $qcm)
    {
        foreach ($qcm->qcmQuestions as $question) {
            if ($question->image && file_exists(public_path('images/questions/' . $question->image))) {
                unlink(public_path('images/questions/' . $question->image));
            }
            if ($question->video && file_exists(public_path('videos/questions/' . $question->video))) {
                unlink(public_path('videos/questions/' . $question->video));
            }
        }

        $qcm->delete();

        return redirect()->back()
            ->with('success', 'QCM supprimé avec succès.');
    }

    // **********
    public function show(string $slug, int $examenId)
    {
        $examen = Examen::find($examenId);
        if (!$examen) {
            return redirect()->route('prof.examen.show', $slug)->with('error', "Il y a un problème dans l'URL !");
        }

        $categorie = Categorie::where('slug', $slug)->firstOrFail();

        $questionsSelectionneesIds = $examen->qcmQuestionsSelectionnees->pluck('id')->toArray();

        $qcms = Qcm::where('categorie_id', $categorie->id)
            ->with(['qcmQuestions' => function ($q) use ($questionsSelectionneesIds) {
                $q->whereIn('id', $questionsSelectionneesIds)->with('qcmChoices');
            }])
            ->get()
            ->filter(fn($qcm) => $qcm->qcmQuestions->isNotEmpty()) 
            ->values();

        return view('prof.examen.qcm.show', compact('slug', 'examen', 'qcms'));
    }

    public function selectQuestionsForm(string $slug, int $examenId)
    {
        $examen = Examen::find($examenId);
        if (!$examen) {
            return redirect()->route('prof.examen.show', $slug)
                ->with('error', "Il y a un problème dans l'URL !");
        }

        if ($examen->status !== 'brouillon') {
            return redirect()->route('prof.examen.qcm',[$slug, $examen->id])->with('error', "L'examen est deja creé !");
        }

        $categorie = Categorie::where('slug', $slug)->firstOrFail();

        $questionsSelectionneesIds = $examen->qcmQuestionsSelectionnees()
            ->pluck('qcm_questions.id')
            ->toArray();

        $qcms = Qcm::where('categorie_id', $categorie->id)
            ->with(['qcmQuestions' => function ($query) use ($examenId) {
                $query->whereNotIn('id', function ($subQuery) use ($examenId) {
                    $subQuery->select('qcm_question_id')
                        ->from('examen_qcm_questions')
                        ->where('examen_id', '!=', $examenId);
                })->with('qcmChoices');
            }])
            ->paginate(5)
            ->withQueryString();

        $questionsAjoutees = count($questionsSelectionneesIds);

        $questionsRestantes = QcmQuestion::whereHas('qcm', function ($query) use ($categorie) {
            $query->where('categorie_id', $categorie->id);
        })
            ->whereNotIn('id', function ($subQuery) {
                $subQuery->select('qcm_question_id')
                    ->from('examen_qcm_questions');
            })
            ->count();

        return view('prof.examen.qcm.select-questions', compact(
            'slug',
            'examen',
            'qcms',
            'questionsSelectionneesIds',
            'questionsAjoutees',
            'questionsRestantes'
        ));
    }

    public function storeSelectedQuestions(Request $request, string $slug, int $examenId)
    {
        $examen = Examen::find($examenId);
        if (!$examen) {
            return redirect()->route('prof.examen.show', $slug)->with('error', "Il y a un problème dans l'URL !");
        }
        if ($examen->status !== 'brouillon') {
            return back('')->with('error', "L'examen est deja creé !");
        }

        $validated = $request->validate([
            'question_ids' => ['nullable', 'array'],
            'question_ids.*' => ['integer', 'exists:qcm_questions,id'],
        ]);

        $syncData = [];
        foreach ($validated['question_ids'] ?? [] as $index => $questionId) {
            $syncData[$questionId] = ['ordre' => $index + 1];
        }

        $examen->qcmQuestionsSelectionnees()->sync($syncData);

        return redirect()
            ->route('prof.examen.qcm', [$slug, $examen->id])
            ->with('success', 'Questions sélectionnées avec succès.');
    }

    public function removeQuestion(string $slug, int $examenId, int $questionId)
    {
        $examen = Examen::findOrFail($examenId);

        if ($examen->status !== 'brouillon') {
            return back()->with(
                'error',
                "Impossible de supprimer une question après la validation de l'examen."
            );
        }

        $examen->qcmQuestionsSelectionnees()
            ->detach($questionId);

        return back()->with(
            'success',
            'Question supprimée de cet examen.'
        );
    }
}
