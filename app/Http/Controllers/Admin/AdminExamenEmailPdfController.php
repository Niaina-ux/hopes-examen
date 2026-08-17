<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\BulletinExamenMail;
use App\Models\Code;
use App\Models\EmailLog;
use App\Models\ExamAttempt;
use App\Models\Examen;
use App\Models\Fichier;
use App\Models\GlisserDeposer;
use App\Models\ImageExercice;
use App\Models\MotsCroises;
use App\Models\Pointiller;
use App\Models\PointillerEtudiantReponse;
use App\Models\Qcm;
use App\Models\Redaction;
use App\Models\Relier;
use App\Models\RelierReponse;
use App\Models\Text;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminExamenEmailPdfController extends Controller
{
    public function downloadBulletin(string $slug, int $examenId, int $studentId)
    {
        $donnees = $this->chargerDonneesBulletin($examenId, $studentId);
        if ($donnees === null) {
            return back()->with('error', "Le bulletin n'est disponible que pour un examen corrigé.");
        }

        [$examen, $student, $etudiant, $resumeParType, $totalPointsGlobalObtenus, $totalNoteGlobal] = $donnees;
        $mention = $this->calculerMention($totalPointsGlobalObtenus, $totalNoteGlobal);

        $pdf = Pdf::loadView('admin.examen.bulletin-pdf', compact(
            'examen', 'student', 'etudiant', 'resumeParType',
            'totalPointsGlobalObtenus', 'totalNoteGlobal', 'mention'
        ));

        return $pdf->download("bulletin-{$student->name}-{$examen->titre}.pdf");
    }

    public function envoyerBulletin(string $slug, int $examenId, int $studentId)
    {
        $donnees = $this->chargerDonneesBulletin($examenId, $studentId);
        if ($donnees === null) {
            return back()->with('error', "Le bulletin n'est disponible que pour un examen corrigé.");
        }

        [$examen, $student, $etudiant, $resumeParType, $totalPointsGlobalObtenus, $totalNoteGlobal] = $donnees;
        $mention = $this->calculerMention($totalPointsGlobalObtenus, $totalNoteGlobal);

        $pdf = Pdf::loadView('admin.examen.bulletin-pdf', compact(
            'examen', 'student', 'etudiant', 'resumeParType',
            'totalPointsGlobalObtenus', 'totalNoteGlobal', 'mention'
        ));

        Mail::to($student->email)->queue(new BulletinExamenMail(
            $student,
            $examen,
            base64_encode($pdf->output())
        ));

        EmailLog::create([
            'user_id' => $student->id,
            'type' => 'bulletin_examen',
            'examen_id' => $examen->id,
            'sujet' => "Votre bulletin de notes : {$examen->titre}",
        ]);

        return back()->with('success', "Bulletin PDF envoyé à {$student->name}.");
    }

    /**
     * Charge toutes les données nécessaires au bulletin. Retourne null si l'examen n'est pas corrigé.
     */
    private function chargerDonneesBulletin(int $examenId, int $studentId): ?array
    {
        $examen = Examen::find($examenId);
        $student = User::find($studentId);
        $etudiant = $student?->student;

        if (!$examen || !$student || !$etudiant) {
            return null;
        }

        $attempt = ExamAttempt::where('examen_id', $examen->id)
            ->where('student_id', $etudiant->id)
            ->where('numero_tentative', 1)
            ->first();

        if (!$attempt || $attempt->status !== 'corrige') {
            return null;
        }

        $examen->load('typesExercice');
        $resumeParType = [];
        $totalPointsGlobalObtenus = 0;
        $totalNoteGlobal = 0;
        $typesCorrectionManuelle = ['code', 'text', 'redaction', 'fichier', 'image'];

        foreach ($examen->typesExercice as $type) {
            switch ($type->slug) {
                case 'qcm':
                    $qcms = Qcm::where('examen_id', $examen->id)
                        ->with(['qcmQuestions.qcmReponsesEtudiants' => fn($q) => $q->where('exam_attempt_id', $attempt->id)])
                        ->get();
                    if ($qcms->isNotEmpty()) {
                        $obtenus = $qcms->flatMap(fn($q) => $q->qcmQuestions)->flatMap(fn($q) => $q->qcmReponsesEtudiants)->sum('points_obtenus');
                        $resumeParType['qcm'] = ['nom' => 'QCM', 'obtenus' => $obtenus, 'total' => $qcms->sum('note_totale')];
                    }
                    break;

                case 'pointiller':
                    $pointillers = Pointiller::where('examen_id', $examen->id)->with('pointillerQuestions.reponses')->get();
                    if ($pointillers->isNotEmpty()) {
                        $obtenus = PointillerEtudiantReponse::whereIn(
                            'pointiller_reponse_id',
                            $pointillers->pluck('pointillerQuestions')->flatten()->pluck('reponses')->flatten()->pluck('id')
                        )->where('exam_attempt_id', $attempt->id)->sum('points_obtenus');
                        $resumeParType['pointiller'] = ['nom' => 'Pointiller', 'obtenus' => $obtenus, 'total' => $pointillers->sum('note_totale')];
                    }
                    break;

                case 'relier':
                    $reliers = Relier::where('examen_id', $examen->id)->with('relierQuestions.paires')->get();
                    if ($reliers->isNotEmpty()) {
                        $obtenus = RelierReponse::whereIn(
                            'relier_paire_id',
                            $reliers->pluck('relierQuestions')->flatten()->pluck('paires')->flatten()->pluck('id')
                        )->where('exam_attempt_id', $attempt->id)->sum('points_obtenus');
                        $resumeParType['relier'] = ['nom' => 'Relier par flèche', 'obtenus' => $obtenus, 'total' => $reliers->sum('note_totale')];
                    }
                    break;

                case 'code':
                    $codes = Code::where('examen_id', $examen->id)
                        ->with(['codeQuestions.reponses' => fn($q) => $q->where('exam_attempt_id', $attempt->id)])
                        ->get();
                    if ($codes->isNotEmpty()) {
                        $reponses = $codes->flatMap(fn($c) => $c->codeQuestions)->flatMap(fn($q) => $q->reponses);
                        $resumeParType['code'] = [
                            'nom' => 'Code', 'obtenus' => $reponses->sum('points_obtenus'), 'total' => $codes->sum('note_totale'),
                            'corrige' => $reponses->isNotEmpty() && $reponses->every(fn($r) => $r->points_obtenus !== null),
                        ];
                    }
                    break;

                case 'text':
                    $texts = Text::where('examen_id', $examen->id)
                        ->with(['textQuestions.reponses' => fn($q) => $q->where('exam_attempt_id', $attempt->id)])
                        ->get();
                    if ($texts->isNotEmpty()) {
                        $reponses = $texts->flatMap(fn($t) => $t->textQuestions)->flatMap(fn($q) => $q->reponses);
                        $resumeParType['text'] = [
                            'nom' => 'Compréhension de texte', 'obtenus' => $reponses->sum('note_obtenue'), 'total' => $texts->sum('note_totale'),
                            'corrige' => $reponses->isNotEmpty() && $reponses->every(fn($r) => $r->note_obtenue !== null),
                        ];
                    }
                    break;

                case 'redaction':
                    $redactions = Redaction::where('examen_id', $examen->id)
                        ->with(['reponses' => fn($q) => $q->where('exam_attempt_id', $attempt->id)])
                        ->get();
                    if ($redactions->isNotEmpty()) {
                        $reponses = $redactions->flatMap(fn($r) => $r->reponses);
                        $resumeParType['redaction'] = [
                            'nom' => 'Rédaction', 'obtenus' => $reponses->sum('note_obtenue'), 'total' => $redactions->sum('note_totale'),
                            'corrige' => $reponses->isNotEmpty() && $reponses->every(fn($r) => $r->note_obtenue !== null),
                        ];
                    }
                    break;

                case 'glisserdeposer':
                    $glisserDeposers = GlisserDeposer::where('examen_id', $examen->id)
                        ->with(['questions.items.reponses' => fn($q) => $q->where('exam_attempt_id', $attempt->id)])
                        ->get();
                    if ($glisserDeposers->isNotEmpty()) {
                        $obtenus = $glisserDeposers->flatMap(fn($g) => $g->questions)->flatMap(fn($q) => $q->items)->flatMap(fn($i) => $i->reponses)->sum('points_obtenus');
                        $resumeParType['glisserdeposer'] = ['nom' => 'Glisser-déposer', 'obtenus' => $obtenus, 'total' => $glisserDeposers->sum('note_totale')];
                    }
                    break;

                case 'fichier':
                    $fichiers = Fichier::where('examen_id', $examen->id)
                        ->with(['fichierQuestions.reponses' => fn($q) => $q->where('exam_attempt_id', $attempt->id)])
                        ->get();
                    if ($fichiers->isNotEmpty()) {
                        $reponses = $fichiers->flatMap(fn($f) => $f->fichierQuestions)->flatMap(fn($q) => $q->reponses);
                        $resumeParType['fichier'] = [
                            'nom' => 'Download & Upload', 'obtenus' => $reponses->sum('points_obtenus'), 'total' => $fichiers->sum('note_totale'),
                            'corrige' => $reponses->isNotEmpty() && $reponses->every(fn($r) => $r->points_obtenus !== null),
                        ];
                    }
                    break;

                case 'image':
                    $images = ImageExercice::where('examen_id', $examen->id)
                        ->with(['questions.reponses' => fn($q) => $q->where('exam_attempt_id', $attempt->id)])
                        ->get();
                    if ($images->isNotEmpty()) {
                        $reponses = $images->flatMap(fn($i) => $i->questions)->flatMap(fn($q) => $q->reponses);
                        $resumeParType['image'] = [
                            'nom' => 'Image', 'obtenus' => $reponses->sum('points_obtenus'), 'total' => $images->sum('note_totale'),
                            'corrige' => $reponses->isNotEmpty() && $reponses->every(fn($r) => $r->points_obtenus !== null),
                        ];
                    }
                    break;

                case 'motscroises':
                    $motsCroisesListe = MotsCroises::where('examen_id', $examen->id)
                        ->with(['motsCroisesMots.reponses' => fn($q) => $q->where('exam_attempt_id', $attempt->id)])
                        ->get();
                    if ($motsCroisesListe->isNotEmpty()) {
                        $obtenus = $motsCroisesListe->flatMap(fn($m) => $m->motsCroisesMots)->flatMap(fn($mot) => $mot->reponses)->sum('points_obtenus');
                        $resumeParType['motscroises'] = ['nom' => 'Mots croisés', 'obtenus' => $obtenus, 'total' => $motsCroisesListe->sum('note_totale')];
                    }
                    break;
            }
        }

        foreach ($resumeParType as $key => $r) {
            $estAutoCorrige = !in_array($key, $typesCorrectionManuelle);
            $estCorrige = $estAutoCorrige || ($r['corrige'] ?? false);

            $totalNoteGlobal += $r['total'];
            if ($estCorrige) {
                $totalPointsGlobalObtenus += $r['obtenus'];
            }
        }

        return [$examen, $student, $etudiant, $resumeParType, $totalPointsGlobalObtenus, $totalNoteGlobal];
    }

    private function calculerMention(float $totalPointsGlobalObtenus, float $totalNoteGlobal): string
    {
        $pourcentage = $totalNoteGlobal > 0 ? ($totalPointsGlobalObtenus / $totalNoteGlobal) * 100 : 0;

        return match(true) {
            $pourcentage >= 90 => 'Excellent',
            $pourcentage >= 80 => 'Très Bien',
            $pourcentage >= 70 => 'Bien',
            $pourcentage >= 60 => 'Assez Bien',
            $pourcentage >= 50 => 'Passable',
            default => 'Insuffisant',
        };
    }
}
