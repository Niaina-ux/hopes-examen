<?php

namespace App\Http\Controllers\Prof\CorrigeExamen;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use App\Models\Text;
use App\Models\TextReponse;
use App\Traits\LoadsCommentairesExercice;
use App\Traits\ResolvesExamenEtudiant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfCorrigeExamenTextController extends Controller
{
    use ResolvesExamenEtudiant, LoadsCommentairesExercice;

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
        
        $texts = Text::where('examen_id', $examen->id)
            ->orderBy('ordre')
            ->with('textQuestions.reponses' , function($q) use ($attempt) {
                $q->where('exam_attempt_id', $attempt->id);
            })
            ->get();

        [$typeText, $commentsText] = $this->loadCommentairesType($examen, 'text', $attempt);

        return view('prof.student.planexamencorrige.text', compact(
            'slug',
            'examen',
            'student',
            'texts',
            'attempt',
            'typeText',
            'commentsText'
        ));
    }

    public function storeAnnotation(Request $request, Text $text)
    {
        $reponseIds = array_keys($request->input('reponses', []));
        $pointsParReponse = TextReponse::whereIn('id', $reponseIds)
            ->with('question')
            ->get()
            ->mapWithKeys(fn($r) => [$r->id => $r->question->points]);

        $validated = $request->validate([
            'reponses'                   => ['required', 'array'],
            'reponses.*.note_obtenue'    => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($pointsParReponse) {
                    preg_match('/reponses\.(\d+)\.note_obtenue/', $attribute, $matches);
                    $reponseId = $matches[1] ?? null;
                    $noteMax = $pointsParReponse[$reponseId] ?? null;

                    if ($noteMax !== null && $value > $noteMax) {
                        $fail("La note ne peut pas dépasser {$noteMax} points pour cette question.");
                    }
                },
            ],
            'reponses.*.reponse_annotee' => ['nullable', 'string'],
        ], [
            'reponses.*.note_obtenue.required' => 'Veuillez indiquer une note pour chaque question avant de valider.',
            'reponses.*.note_obtenue.numeric'  => 'La note doit être un nombre.',
            'reponses.*.note_obtenue.min'      => 'La note ne peut pas être négative.',
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['reponses'] as $reponseId => $data) {
                $reponse = TextReponse::find($reponseId);
                if (!$reponse) {
                    continue;
                }

                $htmlSecurise = strip_tags(
                    $data['reponse_annotee'] ?? '',
                    '<span><b><u><br><p><div><font>'
                );

                $reponse->update([
                    'reponse_annotee' => $htmlSecurise,
                    'note_obtenue'    => $data['note_obtenue'],
                ]);
            }
        });

        return back()->with('success', 'Correction enregistrée.');
    }
}
