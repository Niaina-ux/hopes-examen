<?php

namespace App\Http\Controllers\Prof\CorrigeExamen;

use App\Http\Controllers\Controller;
use App\Models\Code;
use App\Models\ExamAttempt;
use App\Models\Examen;
use App\Models\Fichier;
use App\Models\ImageExercice;
use App\Models\Redaction;
use App\Models\Text;
use App\Models\User;
use Illuminate\Http\Request;

class ProfCorrectionController extends Controller
{
    public function terminerCorrection(Request $request, string $slug, Examen $examen, User $student)
    {
        $etudiant = $student->student;
        abort_if(!$etudiant, 404);

        $attempt = ExamAttempt::where('examen_id', $examen->id)
            ->where('student_id', $etudiant->id)
            ->where('numero_tentative', 1)
            ->where('status', 'termine')
            ->firstOrFail();

        // ✅ Fanamarinana fa voatsara avokoa ny exercice mitaky correction manuelle
        if (!$this->examenEstEntierementCorrige($examen, $attempt)) {
            return back()->with('error', "Il reste des exercices non corrigés. Veuillez tous les corriger avant de terminer.");
        }

        $attempt->update([
            'status' => 'corrige',
            'date_correction' => now(),
        ]);

        return redirect()
            ->route('prof.examen.studentswithexamen', [$slug, $examen->id])
            ->with('success', 'Correction terminée avec succès.');
    }

    private function examenEstEntierementCorrige(Examen $examen, ExamAttempt $attempt): bool
    {
        $codes = Code::where('examen_id', $examen->id)->with('codeQuestions.reponses')->get();
        foreach ($codes as $code) {
            $reponses = $code->codeQuestions->flatMap(fn($q) => $q->reponses->where('exam_attempt_id', $attempt->id));
            if ($reponses->isNotEmpty() && $reponses->contains(fn($r) => $r->points_obtenus === null)) {
                return false;
            }
        }

        $texts = Text::where('examen_id', $examen->id)->with('textQuestions.reponses')->get();
        foreach ($texts as $text) {
            $reponses = $text->textQuestions->flatMap(fn($q) => $q->reponses->where('exam_attempt_id', $attempt->id));
            if ($reponses->isNotEmpty() && $reponses->contains(fn($r) => $r->note_obtenue === null)) {
                return false;
            }
        }

        $redactions = Redaction::where('examen_id', $examen->id)->with('reponses')->get();
        foreach ($redactions as $redaction) {
            $reponse = $redaction->reponses->where('exam_attempt_id', $attempt->id)->first();
            if ($reponse && $reponse->note_obtenue === null) {
                return false;
            }
        }

        $fichiers = Fichier::where('examen_id', $examen->id)->with('fichierQuestions.reponses')->get();
        foreach ($fichiers as $fichier) {
            $reponses = $fichier->fichierQuestions->flatMap(fn($q) => $q->reponses->where('exam_attempt_id', $attempt->id));
            if ($reponses->isNotEmpty() && $reponses->contains(fn($r) => $r->points_obtenus === null)) {
                return false;
            }
        }

        $images = ImageExercice::where('examen_id', $examen->id)->with('questions.reponses')->get();
        foreach ($images as $image) {
            $reponses = $image->questions->flatMap(fn($q) => $q->reponses->where('exam_attempt_id', $attempt->id));
            if ($reponses->isNotEmpty() && $reponses->contains(fn($r) => $r->points_obtenus === null)) {
                return false;
            }
        }

        return true;
    }
}
