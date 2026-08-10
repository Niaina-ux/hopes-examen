<?php

namespace App\Http\Controllers\Prof\CorrigeExamen;

use App\Http\Controllers\Controller;
use App\Models\Commentaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfExamenCommentaireController extends Controller
{
    public function storeCommentaire(Request $request)
    {
        $validated = $request->validate([
            'commentable_id'   => ['required', 'integer'],
            'commentable_type' => ['required', 'string', Rule::in([
                \App\Models\TypeExercice::class,
            ])],
            'examen_id'        => ['required', 'exists:examens,id'],
            'exam_attempt_id'  => ['required', 'exists:exam_attempts,id'],
            'contenu'          => ['required', 'string', 'max:1000'],
        ]);

        Commentaire::updateOrCreate(
            [
                'commentable_id'   => $validated['commentable_id'],
                'commentable_type' => $validated['commentable_type'],
                'exam_attempt_id'  => $validated['exam_attempt_id'],
            ],
            [
                'examen_id' => $validated['examen_id'],
                'user_id'   => Auth::id(),
                'contenu'   => $validated['contenu'],
            ]
        );

        return back()->with('success', 'Commentaire enregistré.');
    }
}
