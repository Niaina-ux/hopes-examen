<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Student\Concerns\HandlesExamenFlow;
use App\Models\ExamAttempt;
use App\Models\Examen;
use App\Models\Redaction;
use App\Models\RedactionReponse;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentExamenRedactionController extends Controller
{
    use HandlesExamenFlow;

    public function show(Request $request, Examen $examen, string $slug, Redaction $redaction)
    {
        // Filaharan'ity redaction ity ao amin'ny examen
        $tousLesRedaction = Redaction::where('examen_id', $examen->id)
            ->orderBy('ordre')
            ->get();

        $index = $tousLesRedaction->search(fn($r) => $r->id === $redaction->id);
        $index = $index === false ? 0 : $index;
        $total = $tousLesRedaction->count();

        $studentId = Auth::id();
        $student = Student::where('user_id', $studentId)->firstOrFail();

        $attempt = ExamAttempt::where('examen_id', $examen->id)
            ->where('student_id', $student->id)
            ->where('status', 'en_cours')
            ->latest('id')
            ->firstOrFail();

        // Alaina ny valiny efa nosoratan'ny mpianatra teo aloha (raha nisy)
        $reponseExistante = RedactionReponse::where('exam_attempt_id', $attempt->id)
            ->where('redaction_id', $redaction->id)
            ->where('student_id', $studentId)
            ->first();

        return view('student.examen.redaction.show', compact(
            'examen', 'slug', 'redaction', 'index', 'total', 'reponseExistante'
        ));
    }

    public function store(Request $request, Examen $examen, string $slug, Redaction $redaction)
    {
        $validated = $request->validate([
            'reponse_texte' => ['required', 'string'],
        ]);

        $texte = trim($validated['reponse_texte']);
        $nombreMots = $texte === '' ? 0 : str_word_count($texte);

        // ✅ Fanamarinana ny nombre_mots_max
        if ($redaction->nombre_mots_max && $nombreMots > $redaction->nombre_mots_max) {
            return back()->withErrors([
                'reponse_texte' => "Votre réponse dépasse le maximum autorisé de {$redaction->nombre_mots_max} mots (vous avez écrit {$nombreMots} mots).",
            ])->withInput();
        }

        $studentId = Auth::id();
        $student = Student::where('user_id', $studentId)->firstOrFail();

        $attempt = ExamAttempt::where('examen_id', $examen->id)
            ->where('student_id', $student->id)
            ->where('status', 'en_cours')
            ->latest('id')
            ->firstOrFail();

        RedactionReponse::where('redaction_id', $redaction->id)
            ->where('student_id', $studentId)
            ->where('exam_attempt_id', $attempt->id)
            ->delete();

        RedactionReponse::create([
            'redaction_id'      => $redaction->id,
            'exam_attempt_id'   => $attempt->id,
            'student_id'        => $studentId,
            'reponse_texte'     => $texte,
            'nombre_mots'       => $nombreMots,
            'submitted_at'      => now(),
            'note_obtenue'      => null,
            'commentaire_prof'  => null,
        ]);

        $redactionSuivante = Redaction::where('examen_id', $examen->id)
            ->where('ordre', '>', $redaction->ordre)
            ->orderBy('ordre')
            ->first();

        if ($redactionSuivante) {
            return redirect()->route('examen.redaction.show', [
                'examen'    => $examen->id,
                'slug'      => $slug,
                'redaction' => $redactionSuivante->id,
            ]);
        }

        return $this->passerAuTypeExerciceSuivant($examen, $slug, 'redaction');
    }
}
