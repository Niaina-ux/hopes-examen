<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\Examen;
use App\Models\Fichier;
use App\Models\FichierQuestion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfExamenFichierQuestionController extends Controller
{
    public function show(string $slug, Examen $examen, Fichier $fichier)
    {
        
        $questions = $fichier->fichierQuestions()
            ->orderBy('ordre')
            ->get();

        return view('prof.examen.downloadUpload.questions.show', compact('slug','examen', 'fichier', 'questions'));
    }

    public function create(string $slug, Examen $examen, Fichier $fichier)
    {
        return view('prof.examen.downloadUpload.questions.create', compact('slug','examen', 'fichier'));
    }

    public function store(Request $request,string $slug, Examen $examen, Fichier $fichier)
    {
        $validated = $request->validate([
            'instruction' => [
                'required',
                'string',
                Rule::unique('fichier_questions', 'instruction')
                    ->where('fichier_id', $fichier->id),
            ],
            'points' => ['required', 'numeric', 'min:0.1'],
            'fichier_prof' => ['nullable', 'file', 'mimes:pdf,doc,docx,zip,rar', 'max:20240'],
        ], [
            'instruction.required' => 'L\'instruction est obligatoire.',
            'instruction.unique' => 'Cette instruction existe déjà dans ce devoir.',
            'fichier_prof.mimes' => 'Le fichier doit être au format pdf, doc, docx, zip ou rar.',
            'fichier_prof.max' => 'Le fichier ne doit pas dépasser 10 Mo.',
        ]);

        $fichierPath = null;
        if ($request->hasFile('fichier_prof')) {
            $file = $request->file('fichier_prof');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('fichiers/prof'), $fileName);
            $fichierPath = $fileName;
        }

        FichierQuestion::create([
            'fichier_id' => $fichier->id,
            'instruction' => $validated['instruction'],
            'fichier_prof' => $fichierPath,
            'points' => $validated['points'],
            'ordre' => $fichier->fichierQuestions()->count(),
        ]);

        return redirect()
            ->route('prof.examen.fichier', [$slug, $examen->id])
            ->with('success', 'Devoir ajouté avec succès.');
    }


    public function edit(string $slug, Examen $examen, Fichier $fichier, FichierQuestion $question)
    {
        return view('prof.examen.downloadUpload.questions.edit', compact('slug','examen', 'fichier', 'question'));
    }

    public function update(Request $request,string $slug,  Examen $examen, Fichier $fichier, FichierQuestion $question)
    {
        $validated = $request->validate([
            'instruction' => [
                'required',
                'string',
                Rule::unique('fichier_questions', 'instruction')
                    ->where('fichier_id', $fichier->id)
                    ->ignore($question->id),
            ],
            'points' => ['required', 'numeric', 'min:0.1'],
            'fichier_prof' => ['nullable', 'file', 'mimes:pdf,doc,docx,zip,rar', 'max:20240'],
        ], [
            'instruction.required' => 'L\'instruction est obligatoire.',
            'instruction.unique' => 'Cette instruction existe déjà dans ce devoir.',
            'fichier_prof.mimes' => 'Le fichier doit être au format pdf, doc, docx, zip ou rar.',
            'fichier_prof.max' => 'Le fichier ne doit pas dépasser 10 Mo.',
        ]);

        $fichierPath = $question->fichier_prof;
        if ($request->hasFile('fichier_prof')) {
            if ($question->fichier_prof && file_exists(public_path('fichiers/prof/' . $question->fichier_prof))) {
                unlink(public_path('fichiers/prof/' . $question->fichier_prof));
            }
            $file = $request->file('fichier_prof');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('fichiers/prof'), $fileName);
            $fichierPath = $fileName;
        }

        $question->update([
            'instruction' => $validated['instruction'],
            'fichier_prof' => $fichierPath,
            'points' => $validated['points'],
        ]);

        return redirect()
            ->route('prof.examen.fichier', [$slug, $examen->id])
            ->with('success', 'Devoir modifié avec succès.');
    }

    public function destroy(string $slug, Examen $examen, Fichier $fichier, FichierQuestion $question)
    {
        if ($question->fichier_prof && file_exists(public_path('fichiers/prof/' . $question->fichier_prof))) {
            unlink(public_path('fichiers/prof/' . $question->fichier_prof));
        }

        $question->delete();

        return redirect()
            ->back()
            ->with('success', 'Devoir supprimé avec succès.');
    }
}
