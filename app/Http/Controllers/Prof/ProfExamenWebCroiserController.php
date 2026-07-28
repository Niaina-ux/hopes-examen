<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\Examen;
use App\Models\MotsCroisesWeb;
use Illuminate\Http\Request;

class ProfExamenWebCroiserController extends Controller
{
    public function index(Examen $examen) 
    {
        $motsCroisesWebs = MotsCroisesWeb::where('examen_id', $examen->id)->get();
        return view('prof.webb.croiser.index',
        compact('examen', 'motsCroisesWebs'));
    }

    public function create(Examen $examen)
    {
        
        return view('prof.webb.croiser.create', compact('examen'));
    }

    public function store(Request $request, Examen $examen)
    {
        $validated = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'note_totale' => ['nullable', 'integer', 'min:1'],
        ], [
            'titre.required' => 'Le titre est obligatoire.',
            'note_totale.integer' => 'La note totale doit être un nombre entier.',
        ]);

        $motsCroisesWeb = MotsCroisesWeb::create([
            'examen_id' => $examen->id,
            'titre' => $validated['titre'],
            'description' => $validated['description'] ?? null,
            'note_totale' => $validated['note_totale'] ?? null,
        ]);

        return redirect()
            ->route('prof.examen.web.croiser', [$examen->id, $motsCroisesWeb->id])
            ->with('success', 'QCM créé avec succès. Vous pouvez maintenant ajouter des questions.');
    }


    public function destroy(Examen $examen, MotsCroisesWeb $motsCroisesWeb)
    {
        foreach ($motsCroisesWeb->motsCroisesWebQuestions as $question) {
            if ($question->image && file_exists(public_path('images/questions/' . $question->image))) {
                unlink(public_path('images/questions/' . $question->image));
            }
            if ($question->video && file_exists(public_path('videos/questions/' . $question->video))) {
                unlink(public_path('videos/questions/' . $question->video));
            }
        }

        $motsCroisesWeb->delete();

        return redirect()->back()
            ->with('success', 'Mots Croises supprimé avec succès.');
    }
}
