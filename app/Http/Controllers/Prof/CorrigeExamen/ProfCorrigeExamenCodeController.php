<?php

namespace App\Http\Controllers\Prof\CorrigeExamen;

use App\Http\Controllers\Controller;
use App\Models\Code;
use App\Models\CodeReponse;
use App\Models\ExamAttempt;
use App\Traits\LoadsCommentairesExercice;
use App\Traits\ResolvesExamenEtudiant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfCorrigeExamenCodeController extends Controller
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
        
        $codes = Code::where('examen_id', $examen->id)
            ->orderBy('ordre')
            ->with('codeQuestions.reponses', function($query) use($attempt){
                $query->where('exam_attempt_id', $attempt->id);
            })
            ->get();

        [$typeCode, $commentsCode] = $this->loadCommentairesType($examen, 'code', $attempt);

        return view('prof.student.planexamencorrige.code', compact(
            'slug',
            'examen',
            'student',
            'codes',
            'attempt',
            'typeCode',
            'commentsCode'
        ));
    }

    public function storeAnnotation(Request $request, Code $code)
    {
        $reponseIds = array_keys($request->input('reponses', []));
        $pointsParReponse = CodeReponse::whereIn('id', $reponseIds)
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
            'reponses.*.code_annote' => ['nullable', 'string'],
        ], [
            // ✅ anarana mifanaraka tanteraka amin'ny field: 'points_obtenus', tsy 'note_obtenue'
            'reponses.*.points_obtenus.numeric'  => 'La note doit être un nombre valide (pas de lettres).',
            'reponses.*.points_obtenus.required' => 'Veuillez indiquer une note pour chaque question avant de valider.',
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['reponses'] as $reponseId => $data) {
                $reponse = CodeReponse::find($reponseId);
                if (!$reponse) {
                    continue;
                }

                $htmlSecurise = strip_tags(
                    $data['code_annote'] ?? '',
                    '<span><b><u><br><p><div><font>'
                );

                $reponse->update([
                    'code_annote'    => $htmlSecurise,
                    'points_obtenus' => $data['points_obtenus'],
                ]);
            }
        });

        return back()->with('success', 'Correction enregistrée.');
    }
}
