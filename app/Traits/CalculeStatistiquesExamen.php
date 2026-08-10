<?php


namespace App\Traits;

use App\Models\Examen;
use App\Models\ExamAttempt;
use App\Models\Qcm;
use App\Models\Code;
use App\Models\Text;
use App\Models\Redaction;
use App\Models\Pointiller;
use App\Models\Relier;
use App\Models\Fichier;
use App\Models\ImageExercice;
use App\Models\GlisserDeposer;
use App\Models\MotsCroises;
use App\Models\PointillerEtudiantReponse;
use App\Models\RelierReponse;

trait CalculeStatistiquesExamen
{
    /**
     * Kajiana ny pourcentage azon'ny mpianatra amin'ity attempt ity,
     * mifototra amin'ny fitambaran'ny points azo isaky ny type d'exercice.
     * Mamerina null raha mbola tsy 'corrige' ilay attempt, na tsy misy note_totale.
     */
    protected function calculerPourcentageAttempt(Examen $examen, ExamAttempt $attempt): ?float
    {
        if ($attempt->status !== 'corrige') {
            return null;
        }

        $examen->loadMissing('typesExercice');
        $totalObtenus = 0;
        $totalPossible = 0;

        foreach ($examen->typesExercice as $type) {
            switch ($type->slug) {
                case 'qcm':
                    $qcms = Qcm::where('examen_id', $examen->id)
                        ->with(['qcmQuestions.qcmReponsesEtudiants' => fn($q) => $q->where('exam_attempt_id', $attempt->id)])
                        ->get();
                    $totalObtenus += $qcms->flatMap(fn($q) => $q->qcmQuestions)->flatMap(fn($q) => $q->qcmReponsesEtudiants)->sum('points_obtenus');
                    $totalPossible += $qcms->sum('note_totale');
                    break;

                case 'code':
                    $codes = Code::where('examen_id', $examen->id)
                        ->with(['codeQuestions.reponses' => fn($q) => $q->where('exam_attempt_id', $attempt->id)])
                        ->get();
                    $totalObtenus += $codes->flatMap(fn($c) => $c->codeQuestions)->flatMap(fn($q) => $q->reponses)->sum('points_obtenus');
                    $totalPossible += $codes->sum('note_totale');
                    break;

                case 'text':
                    $texts = Text::where('examen_id', $examen->id)
                        ->with(['textQuestions.reponses' => fn($q) => $q->where('exam_attempt_id', $attempt->id)])
                        ->get();
                    $totalObtenus += $texts->flatMap(fn($t) => $t->textQuestions)->flatMap(fn($q) => $q->reponses)->sum('note_obtenue');
                    $totalPossible += $texts->sum('note_totale');
                    break;

                case 'redaction':
                    $redactions = Redaction::where('examen_id', $examen->id)
                        ->with(['reponses' => fn($q) => $q->where('exam_attempt_id', $attempt->id)])
                        ->get();
                    $totalObtenus += $redactions->flatMap(fn($r) => $r->reponses)->sum('note_obtenue');
                    $totalPossible += $redactions->sum('note_totale');
                    break;

                case 'pointiller':
                    $pointillers = Pointiller::where('examen_id', $examen->id)->with('pointillerQuestions.reponses')->get();
                    $totalObtenus += PointillerEtudiantReponse::whereIn(
                        'pointiller_reponse_id',
                        $pointillers->pluck('pointillerQuestions')->flatten()->pluck('reponses')->flatten()->pluck('id')
                    )->where('exam_attempt_id', $attempt->id)->sum('points_obtenus');
                    $totalPossible += $pointillers->sum('note_totale');
                    break;

                case 'relier':
                    $reliers = Relier::where('examen_id', $examen->id)->with('relierQuestions.paires')->get();
                    $totalObtenus += RelierReponse::whereIn(
                        'relier_paire_id',
                        $reliers->pluck('relierQuestions')->flatten()->pluck('paires')->flatten()->pluck('id')
                    )->where('exam_attempt_id', $attempt->id)->sum('points_obtenus');
                    $totalPossible += $reliers->sum('note_totale');
                    break;

                case 'fichier':
                    $fichiers = Fichier::where('examen_id', $examen->id)
                        ->with(['fichierQuestions.reponses' => fn($q) => $q->where('exam_attempt_id', $attempt->id)])
                        ->get();
                    $totalObtenus += $fichiers->flatMap(fn($f) => $f->fichierQuestions)->flatMap(fn($q) => $q->reponses)->sum('points_obtenus');
                    $totalPossible += $fichiers->sum('note_totale');
                    break;

                case 'image':
                    $images = ImageExercice::where('examen_id', $examen->id)
                        ->with(['questions.reponses' => fn($q) => $q->where('exam_attempt_id', $attempt->id)])
                        ->get();
                    $totalObtenus += $images->flatMap(fn($i) => $i->questions)->flatMap(fn($q) => $q->reponses)->sum('points_obtenus');
                    $totalPossible += $images->sum('note_totale');
                    break;

                case 'glisserdeposer':
                    $glisserDeposers = GlisserDeposer::where('examen_id', $examen->id)
                        ->with(['questions.items.reponses' => fn($q) => $q->where('exam_attempt_id', $attempt->id)])
                        ->get();
                    $totalObtenus += $glisserDeposers->flatMap(fn($g) => $g->questions)->flatMap(fn($q) => $q->items)->flatMap(fn($i) => $i->reponses)->sum('points_obtenus');
                    $totalPossible += $glisserDeposers->sum('note_totale');
                    break;

                case 'motscroises':
                    $motsCroisesListe = MotsCroises::where('examen_id', $examen->id)
                        ->with(['motsCroisesMots.reponses' => fn($q) => $q->where('exam_attempt_id', $attempt->id)])
                        ->get();
                    $totalObtenus += $motsCroisesListe->flatMap(fn($m) => $m->motsCroisesMots)->flatMap(fn($mot) => $mot->reponses)->sum('points_obtenus');
                    $totalPossible += $motsCroisesListe->sum('note_totale');
                    break;
            }
        }

        return $totalPossible > 0 ? round(($totalObtenus / $totalPossible) * 100, 1) : null;
    }
}
