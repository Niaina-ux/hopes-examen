<?php

namespace App\Http\Controllers\Prof\CorrigeExamen;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use App\Models\ImageExercice;
use App\Models\ImageExerciceReponse;
use App\Traits\LoadsCommentairesExercice;
use App\Traits\ResolvesExamenEtudiant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfCorrigeExamenImageController extends Controller
{
    use ResolvesExamenEtudiant , LoadsCommentairesExercice;

    public function showtache(string $slug, string $examenId, string $studentId)
    {
        //******** */
        $result = $this->resolveExamenEtudiant($slug, $examenId, $studentId);
        if ($result instanceof RedirectResponse) {
            return $result;
        }
        [$examen, $student, $etudiant] = $result;
        //******** */

        $attempt = ExamAttempt::where('examen_id', $examenId)
            ->where('student_id', $etudiant->id)
            ->where('numero_tentative', 1)
            ->where('status','!=','en_cour')
            ->firstOrFail();
        
        $image = ImageExercice::where('examen_id', $examen->id)
            ->orderBy('ordre')
            ->with([
                'questions'=>fn($q)=>$q->orderBy('ordre'),
                'questions.reponses' => function($q) use ($attempt){
                    $q->where('exam_attempt_id', $attempt->id);
                }
            ])
            ->get();

        [$typeImage, $commentsImage] = $this->loadCommentairesType($examen, 'image', $attempt);

        return view('prof.student.planexamencorrige.image', compact(
            'slug',
            'examen',
            'student',
            'image',
            'attempt',
            'typeImage',
            'commentsImage'
        ));
    }

    public function storeAnnotation(Request $request, ImageExercice $imageExercice)
    {
        $reponseIds = array_keys($request->input('reponses', []));
        $pointsParReponse = ImageExerciceReponse::whereIn('id', $reponseIds)
            ->with('question') 
            ->get()
            ->mapWithKeys(fn($r) => [$r->id => $r->question->points]);

        $validated = $request->validate([
            'reponses'                    => ['required', 'array'],
            'reponses.*.points_obtenus'   => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($pointsParReponse) {
                    preg_match('/reponses\.(\d+)\.points_obtenus/', $attribute, $matches);
                    $reponseId = $matches[1] ?? null;
                    $noteMax = $pointsParReponse[$reponseId] ?? null;

                    if ($noteMax !== null && $value > $noteMax) {
                        $fail("La note ne peut pas dépasser {$noteMax} points pour cette question.");
                    }
                },
            ],
            'reponses.*.commentaire_prof' => ['nullable', 'string', 'max:1000'],
        ], [
            'reponses.*.points_obtenus.required' => 'Veuillez indiquer une note pour chaque question avant de valider.',
            'reponses.*.points_obtenus.numeric'  => 'La note doit être un nombre valide (pas de lettres).',
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['reponses'] as $reponseId => $data) {
                $reponse = ImageExerciceReponse::find($reponseId);
                if (!$reponse) {
                    continue;
                }

                $reponse->update([
                    'points_obtenus'   => $data['points_obtenus'],
                    'commentaire_prof' => $data['commentaire_prof'] ?? null,
                ]);
            }
        });

        return back()->with('success', 'Correction enregistrée.');
    }
}
