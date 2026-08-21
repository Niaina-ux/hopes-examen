<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Examen;
use App\Models\Relier;
use App\Models\RelierQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfExamenRelierController extends Controller
{
    public function showbanque(string $slug)
    { 
        $categorie = Categorie::where('slug', $slug)->firstOrFail();
        $types = $categorie->typesExerciceAutorises;

        $reliers = Relier::with('relierQuestions')
            ->latest()
            ->get();

        return view('prof.questions.relier.show', compact('types', 'slug', 'reliers'));
    }

    public function create(string $slug )
    {
        return view('prof.questions.relier.create', compact('slug',));
    }

    public function store(Request $request, string $slug)
    {
        $categorie = Categorie::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ], [
            'titre.required' => 'Le titre est obligatoire.',
        ]);

        $relier = Relier::create([
            'categorie_id' => $categorie->id,
            'titre' => $validated['titre'],
            'description' => $validated['description'] ?? null,
            'duree_minutes' => $validated['duree_minutes'] ?? null,
            'note_totale' => $validated['note_totale'] ?? null,
            'ordre' => 0,
        ]);

        return redirect()
            ->route('prof.question.relier', $slug)
            ->with('success', 'Relier par fléche créé avec succès. Vous pouvez maintenant ajouter des questions.');
    }

    public function edit(string $slug, int $relierId)
    {
        $categorie = Categorie::where('slug', $slug)->firstOrFail();

        $relier = Relier::where('categorie_id', $categorie->id)->find($relierId);

        if (!$relier) {
            return redirect()
                ->route('prof.question.relier', $slug)
                ->with('error', "Ce exercice est introuvable pour cette catégorie.");
        }

        return view('prof.questions.relier.edit', compact('slug', 'relier'));
    }


    public function update(Request $request, string $slug, int $relierId)
    {
        $categorie = Categorie::where('slug', $slug)->firstOrFail();

        $relier = Relier::where('categorie_id', $categorie->id)->find($relierId);

        if (!$relier) {
            return redirect()
                ->route('prof.question.relier', $slug)
                ->with('error', "Ce exericice est introuvable pour cette catégorie.");
        }

        $validated = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ], [
            'titre.required' => 'Le titre est obligatoire.',
        ]);

        $relier->update([
            'titre' => $validated['titre'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('prof.question.relier', $slug)
            ->with('success', 'Relier par flèche modifié avec succès.');
    }

    public function destroy(string $slug,  int $relierId)
    {
        $categorie = Categorie::where('slug', $slug)->firstOrFail();

        $relier = Relier::where('categorie_id', $categorie->id)->find($relierId);

        if (!$relier) {
            return redirect()
                ->route('prof.question.relier', $slug)
                ->with('error', "Ce exericice est introuvable pour cette catégorie.");
        }

        $relier->delete();

        return redirect()->back()
            ->with('success', 'Relier supprimé avec succès.');
    }

    public function show(string $slug, int $examenId)
    {
        $examen = Examen::find($examenId);
        if (!$examen) {
            return redirect()
                ->route('prof.examen.show', $slug)
                ->with('error', "Il y a un problème dans l'URL !");
        }

        $categorie = Categorie::where('slug', $slug)->firstOrFail();

        $questionsSelectionneesIds = $examen->relierQuestionsSelectionnees
            ->pluck('id')
            ->toArray();

        $reliers = Relier::where('categorie_id', $categorie->id)
            ->with(['relierQuestions' => function ($q) use ($questionsSelectionneesIds) {
                $q->whereIn('id', $questionsSelectionneesIds)
                    ->with('paires');
            }])
            ->get()
            ->filter(fn($relier) => $relier->relierQuestions->isNotEmpty())
            ->values();

        return view('prof.examen.relier.show', compact('slug', 'examen', 'reliers'));
    }


    public function selectQuestionsForm(string $slug, int $examenId)
    {
        $examen = Examen::find($examenId);
        if (!$examen) {
            return redirect()
                ->route('prof.examen.show', $slug)
                ->with('error', "Il y a un problème dans l'URL !");
        }

        if ($examen->status !== 'brouillon') {
            return redirect()
                ->route('prof.examen.relier', [$slug, $examen->id])
                ->with('error', "L'examen est deja créé !");
        }

        $categorie = Categorie::where('slug', $slug)->firstOrFail();

        $questionsSelectionneesIds = $examen->relierQuestionsSelectionnees()
            ->pluck('relier_questions.id')
            ->toArray();

        $questionsDansAutresExamens = DB::table('examen_relier_questions')
            ->where('examen_id', '!=', $examen->id)
            ->pluck('relier_question_id')
            ->toArray();

        $reliers = Relier::where('categorie_id', $categorie->id)
            ->with(['relierQuestions' => function ($query) use (
                $questionsSelectionneesIds,
                $questionsDansAutresExamens
            ) {
                $query->where(function ($q) use (
                    $questionsSelectionneesIds,
                    $questionsDansAutresExamens
                ) {
                    $q->whereIn('id', $questionsSelectionneesIds)
                        ->orWhereNotIn('id', $questionsDansAutresExamens);
                })
                ->with('paires');
            }])
            ->get()
            ->filter(fn($relier) => $relier->relierQuestions->isNotEmpty())
            ->values();

        $questionsAjoutees = count($questionsSelectionneesIds);

        $questionsDisponibles = RelierQuestion::whereHas('relier', function ($q) use ($categorie) {
            $q->where('categorie_id', $categorie->id);
        })
        ->whereNotIn('id', $questionsDansAutresExamens)
        ->count();

        $questionsRestantes = $questionsDisponibles - $questionsAjoutees;

        return view('prof.examen.relier.select-question', compact(
            'slug',
            'examen',
            'reliers',
            'questionsSelectionneesIds',
            'questionsAjoutees',
            'questionsRestantes'
        ));
    }

    public function storeSelectedQuestions(Request $request, string $slug, int $examenId)
    {
        $examen = Examen::find($examenId);

        if (!$examen) {
            return redirect()
                ->route('prof.examen.show', $slug)
                ->with('error', "Il y a un problème dans l'URL !");
        }

        $validated = $request->validate([
            'question_ids' => ['nullable', 'array'],
            'question_ids.*' => ['exists:relier_questions,id'],
        ]);

        $categorie = Categorie::where('slug', $slug)->firstOrFail();
        $questionIds = $validated['question_ids'] ?? [];

        DB::transaction(function () use ($examen, $categorie, $questionIds) {
            $questionIdsValides = RelierQuestion::whereIn('id', $questionIds)
                ->whereHas('relier', function ($q) use ($categorie) {
                    $q->where('categorie_id', $categorie->id);
                })
                ->pluck('id')
                ->toArray();

            DB::table('examen_relier_questions')
                ->where('examen_id', $examen->id)
                ->delete();

            foreach ($questionIdsValides as $index => $questionId) {
                DB::table('examen_relier_questions')->insert([
                    'examen_id' => $examen->id,
                    'relier_question_id' => $questionId,
                    'ordre' => $index + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return redirect()
            ->route('prof.examen.relier', [$slug, $examen->id])
            ->with('success', 'Sélection des questions relier par flèche enregistrée avec succès.');
    }

    public function removeQuestion( string $slug, int $examenId, int $questionId ) 
    {
        $examen = Examen::find($examenId);
        if (!$examen) {
            return redirect()
                ->route('prof.examen.show', $slug)
                ->with('error', "Il y a un problème dans l'URL !");
        }
        if ($examen->status !== 'brouillon') {
            return redirect()
                ->back()
                ->with('error', "Impossible de supprimer une question après la création de l'examen.");
        }

        $question = RelierQuestion::find($questionId);

        if (!$question) {
            return redirect()
                ->back()
                ->with('error', "Cette question est introuvable.");
        }

        $supprime = DB::table('examen_relier_questions')
            ->where('examen_id', $examen->id)
            ->where('relier_question_id', $question->id)
            ->delete();

        if (!$supprime) {
            return redirect()
                ->back()
                ->with('error', "Cette question n'est pas associée à cet examen.");
        }

        return redirect()
            ->back()
            ->with('success', 'Question supprimée de cet examen avec succès.');
    }

}
