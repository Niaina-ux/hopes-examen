<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\Code;
use App\Models\CodeQuestion;
use App\Models\Examen;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfExamenCodeQuestionController extends Controller
{
    public function show(string $slug, Examen $examen, Code $code)
    {
        $questions = $code->codeQuestions()
            // ->withCount('reponses')
            ->orderBy('ordre')
            ->get();

        return view('prof.examen.code.questions.show', compact('slug','examen', 'code', 'questions'));
    }

    public function create(string $slug, Examen $examen, Code $code)
    {
        return view('prof.examen.code.questions.create', compact('slug','examen', 'code'));
    }

    public function store(Request $request, string $slug, Examen $examen, Code $code)
    {
        $validated = $request->validate([
            'instruction' => [
                'required',
                'string',
                Rule::unique('code_questions', 'instruction')
                    ->where('code_id', $code->id),
            ],
            'langage' => ['required', 'string', 'in:php,javascript,python,html,css,java,c,cpp,laravel,ensemble'],
            'code_starter' => ['nullable', 'string'],
            'points' => ['required', 'numeric', 'min:0.1'],
        ], [
            'instruction.required' => 'L\'instruction est obligatoire.',
            'instruction.unique' => 'Cette instruction existe déjà dans cet exercice.',
            'langage.required' => 'Le langage est obligatoire.',
        ]);

        CodeQuestion::create([
            'code_id' => $code->id,
            'instruction' => $validated['instruction'],
            'langage' => $validated['langage'],
            'code_starter' => $validated['code_starter'] ?? null,
            'points' => $validated['points'],
            'ordre'        => $code->codeQuestions()->count() + 1,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Exercice de code ajouté avec succès.');
    }

    public function edit(string $slug, Examen $examen, Code $code, CodeQuestion $question)
    {
        return view('prof.examen.code.questions.edit', compact('slug', 'examen', 'code', 'question'));
    }

    public function update(Request $request, string $slug, Examen $examen, Code $code, CodeQuestion $question)
    {
        $validated = $request->validate([
            'instruction' => [
                'required',
                'string',
                Rule::unique('code_questions', 'instruction')
                    ->where('code_id', $code->id)
                    ->ignore($question->id), 
            ],
            'langage'      => ['required', 'string', 'in:php,javascript,python,html,css,java,c,cpp,laravel,ensemble'],
            'code_starter' => ['nullable', 'string'],
            'points'       => ['required', 'numeric', 'min:0.1'],
        ], [
            'instruction.required' => 'L\'instruction est obligatoire.',
            'instruction.unique'   => 'Cette instruction existe déjà dans cet exercice.',
            'langage.required'     => 'Le langage est obligatoire.',
        ]);

        $question->update([
            'instruction'  => $validated['instruction'],
            'langage'      => $validated['langage'],
            'code_starter' => $validated['code_starter'] ?? null,
            'points'       => $validated['points'],
        ]);

        return redirect()
            ->route('prof.examen.code.question.show', [$slug, $examen->id, $code->id])
            ->with('success', 'Exercice de code modifié avec succès.');
    }

    public function destroy(string $slug , Examen $examen, Code $code, CodeQuestion $question)
    {
        $question->delete();

        return redirect()
            ->back()
            ->with('success', 'Exercice de code supprimé.');
    }
}
