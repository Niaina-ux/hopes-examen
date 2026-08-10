<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Code;
use App\Models\ExamAttempt;
use App\Models\Fichier;
use App\Models\GlisserDeposer;
use App\Models\ImageExercice;
use App\Models\MotsCroises;
use App\Models\Pointiller;
use App\Models\Qcm;
use App\Models\Redaction;
use App\Models\Relier;
use App\Models\Student;
use App\Models\StudentExamen;
use App\Models\Text;
use App\Traits\CalculeStatistiquesExamen;
use App\Traits\LoadsCommentairesExercice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class StudentExamenHistoriqueController extends Controller
{
    use LoadsCommentairesExercice , CalculeStatistiquesExamen;

    public function dashboard()
    {
        $student = Student::where('user_id', Auth::id())->firstOrFail();

        $attempts = ExamAttempt::where('student_id', $student->id)
            ->where('numero_tentative', 1)
            ->where('status', '!=', 'en_cours')
            ->with('examen.categorie')
            ->latest('date_fin')
            ->get();

        // ✅ Statistiques : pourcentage par examen, seulement pour les attempts corrigés
        $statistiques = $attempts
            ->filter(fn($attempt) => $attempt->status === 'corrige')
            ->map(function ($attempt) {
                return [
                    'titre' => $attempt->examen->titre,
                    'date' => $attempt->date_fin?->format('d/m/Y'),
                    'pourcentage' => $this->calculerPourcentageAttempt($attempt->examen, $attempt),
                ];
            })
            ->filter(fn($s) => $s['pourcentage'] !== null)
            ->sortBy('date') // ✅ ordre chronologique, du plus ancien au plus récent
            ->values();

        $moyenneGenerale = $statistiques->isNotEmpty()
            ? round($statistiques->avg('pourcentage'), 1)
            : null;

        $examen_planifie = StudentExamen::with('examen.categorie')
            ->where('user_id', Auth::id())
            ->where('termine', false)
            ->whereHas('examen', function ($query) {
                $query->where('status', '!=', 'brouillon');
            })
            ->orderBy('id', 'asc')
            ->get();

        return view('student.dashboard', compact('attempts', 'examen_planifie', 'statistiques', 'moyenneGenerale'));
    }

    public function show(ExamAttempt $attempt)
    {
        $student = Student::where('user_id', Auth::id())->firstOrFail();

        if ($attempt->student_id !== $student->id) {
            abort(403, 'Vous n\'êtes pas autorisé(e) à consulter cet examen.');
        }

        $examen = $attempt->examen;
        $examen->load('typesExercice');

        $qcms = collect();
        $pointillers = collect();
        $reliers = collect();
        $codes = collect();
        $texts = collect();
        $redactions = collect();
        $glisserDeposers = collect();
        $motsCroisesListe = collect();
        $fichiers = collect();
        $image = collect();

        $resume = [
            'total_obtenu' => 0,
            'total_possible' => 0,
            'details' => [],
        ];

        foreach ($examen->typesExercice as $type) {
            switch ($type->slug) {
                case 'qcm':
                    $qcms = Qcm::where('examen_id', $examen->id)
                        ->orderBy('ordre')
                        ->with([
                            'qcmQuestions' => fn($q) => $q->orderBy('ordre'),
                            'qcmQuestions.qcmChoices',
                            'qcmQuestions.qcmReponsesEtudiants' => function ($query) use ($attempt) {
                                $query->where('exam_attempt_id', $attempt->id)
                                    ->where('student_id', Auth::id())
                                    ->with('qcmchoice');
                            },
                        ])
                        ->get();
                    break;

                case 'pointiller':
                    $pointillers = Pointiller::where('examen_id', $examen->id)
                        ->orderBy('ordre')
                        ->with([
                            'pointillerQuestions' => fn($q) => $q->orderBy('ordre'),
                            'pointillerQuestions.reponses',
                            'pointillerQuestions.reponses.etudiantReponses' => function ($q) use ($attempt) {
                                $q->where('exam_attempt_id', $attempt->id);
                            },
                        ])
                        ->get();
                    break;

                case 'relier':
                    $reliers = Relier::where('examen_id', $examen->id)
                        ->orderBy('ordre')
                        ->with([
                            'relierQuestions' => fn($q) => $q->orderBy('ordre'),
                            'relierQuestions.paires',
                        ])
                        ->get();
                    break;

                case 'code':
                    // ✅ 'codeQuestions.reponses' => function(){...} — tsy ',' intsony
                    $codes = Code::where('examen_id', $examen->id)
                        ->orderBy('ordre')
                        ->with(['codeQuestions.reponses' => function ($query) {
                            $query->where('student_id', Auth::id());
                        }])
                        ->get();
                    break;

                case 'text':
                    // ✅ 'textQuestions.reponses' => function(){...}
                    $texts = Text::where('examen_id', $examen->id)
                        ->orderBy('ordre')
                        ->with(['textQuestions.reponses' => function ($query) {
                            $query->where('student_id', Auth::id());
                        }])
                        ->get();
                    break;

                case 'redaction':
                    // ✅ 'reponses' => function(){...}
                    $redactions = Redaction::where('examen_id', $examen->id)
                        ->orderBy('ordre')
                        ->with(['reponses' => function ($query) {
                            $query->where('student_id', Auth::id());
                        }])
                        ->get();
                    break;

                case 'glisserdeposer':
                    $glisserDeposers = GlisserDeposer::where('examen_id', $examen->id)
                        ->orderBy('ordre')
                        ->with([
                            'questions' => fn($q) => $q->orderBy('ordre'),
                            'questions.zones',
                            'questions.items' => function ($q) use ($attempt) {
                                $q->with(['zone', 'reponses' => function ($rq) use ($attempt) {
                                    $rq->where('exam_attempt_id', $attempt->id)
                                        ->where('student_id', Auth::id())
                                        ->with('zoneChoisie');
                                }]);
                            },
                        ])
                        ->get();
                    break;

                case 'fichier':
                    $fichiers = Fichier::where('examen_id', $examen->id)
                        ->orderBy('ordre')
                        ->with([
                            'fichierQuestions' => fn($q) => $q->orderBy('ordre'),
                            'fichierQuestions.reponses' => function ($q) use ($attempt) {
                                $q->where('exam_attempt_id', $attempt->id)
                                    ->where('student_id', Auth::id());
                            },
                        ])
                        ->get();
                    break;

                case 'motscroises':
                    $motsCroisesListe = MotsCroises::where('examen_id', $examen->id)
                        ->orderBy('ordre')
                        ->with([
                            'motsCroisesMots' => fn($q) => $q->orderBy('numero'),
                            'motsCroisesMots.reponses' => function ($q) use ($attempt) {
                                $q->where('exam_attempt_id', $attempt->id)
                                    ->where('student_id', Auth::id());
                            },
                        ])
                        ->get();
                    break;

                case 'image':
                    $image = ImageExercice::where('examen_id', $examen->id)
                        ->orderBy('ordre')
                        ->with([
                            'questions' => fn($q) => $q->orderBy('ordre'),
                            'questions.reponses' => function ($q) use ($attempt) {
                                $q->where('exam_attempt_id', $attempt->id);
                            },
                        ])
                        ->get();
                    break;
            }
        }

        [$typeQcm, $commentsQcm]                     = $this->loadCommentairesType($examen, 'qcm', $attempt);
        [$typePointiller, $commentsPointiller]       = $this->loadCommentairesType($examen, 'pointiller', $attempt);
        [$typeRelier, $commentsRelier]               = $this->loadCommentairesType($examen, 'relier', $attempt);
        [$typeCode, $commentsCode]                   = $this->loadCommentairesType($examen, 'code', $attempt);
        [$typeText, $commentsText]                   = $this->loadCommentairesType($examen, 'text', $attempt);
        [$typeRedaction, $commentsRedaction]         = $this->loadCommentairesType($examen, 'redaction', $attempt);
        [$typeGlisserDeposer, $commentsGlisserDeposer] = $this->loadCommentairesType($examen, 'glisserdeposer', $attempt);
        [$typeFichier, $commentsFichier]             = $this->loadCommentairesType($examen, 'fichier', $attempt);
        [$typeMotsCroises, $commentsMotsCroises]     = $this->loadCommentairesType($examen, 'motscroises', $attempt);
        [$typeImage, $commentsImage]                 = $this->loadCommentairesType($examen, 'image', $attempt);

        return view('student.fiche-examen', compact(
            'attempt', 'examen', 'qcms', 'pointillers', 'reliers', 'codes', 'texts',
            'redactions', 'glisserDeposers', 'fichiers', 'motsCroisesListe', 'resume', 'image',
            'typeQcm', 'commentsQcm',
            'typePointiller', 'commentsPointiller',
            'typeRelier', 'commentsRelier',
            'typeCode', 'commentsCode',
            'typeText', 'commentsText',
            'typeRedaction', 'commentsRedaction',
            'typeGlisserDeposer', 'commentsGlisserDeposer',
            'typeFichier', 'commentsFichier',
            'typeMotsCroises', 'commentsMotsCroises',
            'typeImage', 'commentsImage'
        ));
    }
}
