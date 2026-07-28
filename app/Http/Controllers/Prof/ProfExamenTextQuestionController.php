<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\Examen;
use App\Models\Text;
use App\Models\TextQuestion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfExamenTextQuestionController extends Controller
{
    public function show(string $slug, Examen $examen, Text $text)
    {
        $questions = $text->textQuestions()->orderBy('ordre')->get();

        return view('prof.examen.text.questions.show', compact('slug', 'examen', 'text', 'questions'));
    }

    public function create(string $slug, Examen $examen, Text $text)
    {
        return view('prof.examen.text.questions.create', compact('slug', 'examen', 'text'));
    }

    public function store(Request $request, string $slug, Examen $examen, Text $text)
    {
        $validated = $request->validate([
            'enonce' => [
                'required',
                'string',
                Rule::unique('text_questions', 'enonce')->where('text_id', $text->id),
            ],
            'points' => ['required', 'numeric', 'min:0.1'],
        ], [
            'enonce.required' => 'L\'énoncé est obligatoire.',
            'enonce.unique' => 'Cette question existe déjà pour ce texte.',
        ]);

        $pointsExistants = $text->textQuestions()->sum('points');
        $pointsTotal = $pointsExistants + $validated['points'];

        if ($text->note_totale !== null && $pointsTotal > $text->note_totale) {
            return back()->withErrors([
                'points' => "Le total des points ({$pointsTotal}) dépasse la note totale autorisée pour ce texte ({$text->note_totale}).",
            ])->withInput();
        }

        TextQuestion::create([
            'text_id' => $text->id,
            'enonce' => $validated['enonce'],
            'points' => $validated['points'],
            'ordre' => $text->textQuestions()->count(),
        ]);

        return redirect()
            ->back()
            ->with('success', 'Question ajoutée avec succès. Vous pouvez ajouter d autre');
    }

    public function edit(string $slug, Examen $examen, Text $text, TextQuestion $question)
    {
        return view('prof.examen.text.questions.edit', compact('slug', 'examen', 'text', 'question'));
    }

    public function update(Request $request, string $slug, Examen $examen, Text $text, TextQuestion $question)
    {
        $validated = $request->validate([
            'enonce' => [
                'required',
                'string',
                Rule::unique('text_questions', 'enonce')->where('text_id', $text->id)->ignore($question->id),
            ],
            'points' => ['required', 'numeric', 'min:0.1'],
        ], [
            'enonce.required' => 'L\'énoncé est obligatoire.',
            'enonce.unique' => 'Cette question existe déjà pour ce texte.',
        ]);

        $pointsAutres = $text->textQuestions()->where('id', '!=', $question->id)->sum('points');
        $pointsTotal = $pointsAutres + $validated['points'];

        if ($text->note_totale !== null && $pointsTotal > $text->note_totale) {
            return back()->withErrors([
                'points' => "Le total des points ({$pointsTotal}) dépasse la note totale autorisée pour ce texte ({$text->note_totale}).",
            ])->withInput();
        }

        $question->update([
            'enonce' => $validated['enonce'],
            'points' => $validated['points'],
        ]);

        return redirect()
            ->back()
            ->with('success', 'Question modifiée avec succès.');
    }

    public function destroy(string $slug, Examen $examen, Text $text, TextQuestion $question)
    {
        $question->delete();

        return redirect()
            ->back()
            ->with('success', 'Question supprimée avec succès.');
    }
}
