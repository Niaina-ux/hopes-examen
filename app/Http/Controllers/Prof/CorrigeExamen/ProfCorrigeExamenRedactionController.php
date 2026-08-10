<?php

namespace App\Http\Controllers\Prof\CorrigeExamen;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use App\Models\Examen;
use App\Models\Redaction;
use App\Models\RedactionReponse;
use App\Models\User;
use App\Traits\LoadsCommentairesExercice;
use App\Traits\ResolvesExamenEtudiant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\FuncCall;

class ProfCorrigeExamenRedactionController extends Controller
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
            
        $redactions = Redaction::where('examen_id', $examen->id)
            ->orderBy('ordre')
            ->with('reponses',function($q) use ($attempt){
                $q->where('exam_attempt_id', $attempt->id);
            })
            ->get();

        [$typeRedaction, $commentsRedaction] = $this->loadCommentairesType($examen, 'redaction', $attempt);


        return view('prof.student.planexamencorrige.redaction', compact(
            'slug',
            'examen',
            'student',
            'redactions',
            'attempt',
            'typeRedaction',
            'commentsRedaction'
        ));
    }

    public function storeAnnotation(Request $request, RedactionReponse $reponse)
    {
        $noteMax = $reponse->redaction->note_totale;

        $validated = $request->validate([
            'reponse_annotee' => ['nullable', 'string'],
            'note_obtenue'    => ['required', 'numeric', 'min:0', 'max:' . $noteMax],
        ], [
            'note_obtenue.required' => 'Veuillez indiquer une note avant de valider.',
            'note_obtenue.numeric'  => 'La note doit être un nombre valide (pas de lettres).',
            'note_obtenue.max'      => 'La note ne peut pas dépasser ' . $noteMax . ' points.',
            'note_obtenue.min'      => 'La note ne peut pas être négative.',
        ]);

        $htmlSecurise = strip_tags(
            $validated['reponse_annotee'] ?? '',
            '<span><b><u><br><p><div><font>'
        );

        $reponse->update([
            'reponse_annotee' => $htmlSecurise,
            'note_obtenue'    => $validated['note_obtenue'],
        ]);

        return back()->with('success', 'Correction enregistrée.');
    }
}
