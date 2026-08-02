<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\Examen;
use App\Models\Relier;
use App\Models\RelierQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProfExamenRelierQuestionController extends Controller
{
    public function show(string $slug, Examen $examen, Relier $relier)
    {
        $questions = $relier->relierQuestions()
            ->with('paires')
            ->orderBy('id', 'desc')
            ->get();

        return view('prof.examen.relier.questions.show',
            compact('slug','examen', 'relier', 'questions')
        );
    }

    public function create(string $slug, Examen $examen, Relier $relier)
    {
        return view('prof.examen.relier.questions.create', compact('slug', 'examen', 'relier'));
    }

    public function store(Request $request, string $slug, Examen $examen, Relier $relier)
    {
        $validated = $request->validate([
            'enonce' => [
                'required',
                'string',
                Rule::unique('relier_questions', 'enonce')->where('relier_id', $relier->id),
            ],
            'points' => ['required', 'numeric', 'min:0.1'],
            'ordre' => ['required', 'integer'],

            'element_gauche' => ['required', 'array', 'min:2'],
            'element_gauche.*' => ['required', 'string'],
            'element_droit' => ['required', 'array', 'min:2'],
            'element_droit.*' => ['required', 'string'],
            'order_left.*' => ['required', 'integer'],
            'order_right.*' => ['required', 'integer'],
        ], [
            'enonce.required' => 'L\'énoncé est obligatoire.',
            'enonce.unique' => 'Cette question existe déjà dans cet exercice.',
            'element_gauche.min' => 'Ajoutez au moins 2 paires.',
        ]);

        if (count($validated['element_gauche']) !== count(array_unique($validated['element_gauche']))) {
            return back()->withInput()->withErrors([
                'element_gauche' => 'Tsy mahazo misy élément gauche miverimberina ao anatin\'ity question ity.',
            ]);
        }

        if (count($validated['element_droit']) !== count(array_unique($validated['element_droit']))) {
            return back()->withInput()->withErrors([
                'element_droit' => 'Tsy mahazo misy élément droite miverimberina ao anatin\'ity question ity.',
            ]);
        }

        if (isset($validated['order_left']) && count($validated['order_left']) !== count(array_unique($validated['order_left']))) {
            return back()->withInput()->withErrors([
                'order_left' => 'Les ordres de la colonne gauche ne doivent pas se répéter.',
            ]);
        }

        if (isset($validated['order_right']) && count($validated['order_right']) !== count(array_unique($validated['order_right']))) {
            return back()->withInput()->withErrors([
                'order_right' => 'Les ordres de la colonne droite ne doivent pas se répéter.',
            ]);
        }

        // ✅ Fanamarinana ny note_totale
        if ($relier->note_totale !== null) {
            $pointsExistants = $relier->relierQuestions()->sum('points');
            $pointsTotal = $pointsExistants + $validated['points'];

            if ($pointsTotal > $relier->note_totale) {
                $pointsRestants = $relier->note_totale - $pointsExistants;
                return back()->withInput()->withErrors([
                    'points' => "Le total des points ({$pointsTotal}) dépasse la note totale de l'exercice ({$relier->note_totale}). Il reste {$pointsRestants} point(s) disponible(s).",
                ]);
            }
        }

        DB::transaction(function () use ($validated, $relier) {
            $question = $relier->relierQuestions()->create([
                'enonce' => $validated['enonce'],
                'points' => $validated['points'],
                'ordre' => $validated['ordre'],
            ]);

            foreach ($validated['element_gauche'] as $index => $gauche) {
                $question->paires()->create([
                    'element_gauche' => $gauche,
                    'element_droite' => $validated['element_droit'][$index],
                    'ordre_gauche' => $validated['order_left'][$index] ?? 0,
                    'ordre_droite' => $validated['order_right'][$index] ?? 0,
                ]);
            }
        });

        return redirect()
            ->route('prof.examen.relier', [$slug, $examen->id])
            ->with('success', 'Question enregistrée avec succès.');
    }

    public function edit(string $slug, Examen $examen, Relier $relier, RelierQuestion $question)
    {
        $question->load('paires');

        return view('prof.examen.relier.questions.edit', compact('slug', 'examen', 'relier', 'question'));
    }

    public function update(Request $request, string $slug, Examen $examen, Relier $relier, RelierQuestion $question)
    {
        $validated = $request->validate([
            'enonce' => [
                'required',
                'string',
                Rule::unique('relier_questions', 'enonce')
                    ->where('relier_id', $relier->id)
                    ->ignore($question->id),
            ],
            'points' => ['required', 'numeric', 'min:0.1'],
            'ordre' => ['required', 'integer'],

            'element_gauche' => ['required', 'array', 'min:2'],
            'element_gauche.*' => ['required', 'string'],
            'element_droit' => ['required', 'array', 'min:2'],
            'element_droit.*' => ['required', 'string'],
            'order_left.*' => ['required', 'integer'],
            'order_right.*' => ['required', 'integer'],
        ], [
            'enonce.required' => 'L\'énoncé est obligatoire.',
            'enonce.unique' => 'Cette question existe déjà dans cet exercice.',
            'element_gauche.min' => 'Ajoutez au moins 2 paires.',
        ]);

        if (count($validated['element_gauche']) !== count(array_unique($validated['element_gauche']))) {
            return back()->withInput()->withErrors([
                'element_gauche' => 'Tsy mahazo misy élément gauche miverimberina ao anatin\'ity question ity.',
            ]);
        }

        if (count($validated['element_droit']) !== count(array_unique($validated['element_droit']))) {
            return back()->withInput()->withErrors([
                'element_droit' => 'Tsy mahazo misy élément droite miverimberina ao anatin\'ity question ity.',
            ]);
        }

        if (isset($validated['order_left']) && count($validated['order_left']) !== count(array_unique($validated['order_left']))) {
            return back()->withInput()->withErrors([
                'order_left' => 'Les ordres de la colonne gauche ne doivent pas se répéter.',
            ]);
        }

        if (isset($validated['order_right']) && count($validated['order_right']) !== count(array_unique($validated['order_right']))) {
            return back()->withInput()->withErrors([
                'order_right' => 'Les ordres de la colonne droite ne doivent pas se répéter.',
            ]);
        }

        // ✅ Fanamarinana ny note_totale — tsy anisan'ny sum ilay question ankehitriny
        if ($relier->note_totale !== null) {
            $pointsAutresQuestions = $relier->relierQuestions()
                ->where('id', '!=', $question->id)
                ->sum('points');

            $pointsTotal = $pointsAutresQuestions + $validated['points'];

            if ($pointsTotal > $relier->note_totale) {
                $pointsRestants = $relier->note_totale - $pointsAutresQuestions;
                return back()->withInput()->withErrors([
                    'points' => "Le total des points ({$pointsTotal}) dépasse la note totale de l'exercice ({$relier->note_totale}). Maximum autorisé pour cette question : {$pointsRestants} point(s).",
                ]);
            }
        }

        DB::transaction(function () use ($validated, $question) {
            $question->update([
                'enonce' => $validated['enonce'],
                'points' => $validated['points'],
                'ordre' => $validated['ordre'],
            ]);

            $question->paires()->delete();

            foreach ($validated['element_gauche'] as $index => $gauche) {
                $question->paires()->create([
                    'element_gauche' => $gauche,
                    'element_droite' => $validated['element_droit'][$index],
                    'ordre_gauche' => $validated['order_left'][$index] ?? 0,
                    'ordre_droite' => $validated['order_right'][$index] ?? 0,
                ]);
            }
        });

        return redirect()
            ->route('prof.examen.relier.question.show', [$slug, $examen->id, $relier->id])
            ->with('success', 'Question modifiée avec succès.');
    }


    public function destroy(string $slug, Examen $examen, Relier $relier, RelierQuestion $question)
    {
        $question->delete();

        return redirect()
            ->route('prof.examen.relier.question.show', [$slug, $examen->id, $relier->id])
            ->with('success', 'Question supprimée avec succès.');
    }
}
