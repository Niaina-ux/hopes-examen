<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Code;
use App\Models\ExamAttempt;
use App\Models\Fichier;
use App\Models\GlisserDeposer;
use App\Models\MotsCroises;
use App\Models\Pointiller;
use App\Models\Qcm;
use App\Models\Redaction;
use App\Models\Relier;
use App\Models\Student;
use App\Models\StudentExamen;
use App\Models\Text;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentExamenHistoriqueController extends Controller
{
    public function dashboard()
    {
        $student = Student::where('user_id', Auth::id())->firstOrFail();

        $attempts = ExamAttempt::where('student_id', $student->id)
            ->where('numero_tentative', 1)
            ->where('status', '!=', 'en_cours')
            ->with('examen.categorie')
            ->latest('date_fin')
            ->get();

        $examen_planifie = StudentExamen::with('examen.categorie')
            ->where('user_id', Auth::id())
            ->where('termine', false)
            ->whereHas('examen', function ($query) {
                $query->where('status', '!=', 'brouillon');
            })
            ->orderBy('id', 'asc')
            ->get();
       
        return view('student.dashboard', compact('attempts', 'examen_planifie'));
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
                                $query->where('exam_attempt_id', $attempt->id)->with('qcmchoice');
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
                    $codes = Code::where('examen_id', $examen->id)
                        ->orderBy('ordre')
                        ->with('codeQuestions.reponses')
                        ->get();
                    break;

                case 'text':
                    $texts = Text::where('examen_id', $examen->id)
                        ->orderBy('ordre')
                        ->with('textQuestions.reponses')
                        ->get();
                    break;

                case 'redaction':
                    $redactions = Redaction::where('examen_id', $examen->id)
                        ->orderBy('ordre')
                        ->with('reponses')
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
                                    $rq->where('exam_attempt_id', $attempt->id)->with('zoneChoisie');
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
                                $q->where('exam_attempt_id', $attempt->id);
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
                                $q->where('exam_attempt_id', $attempt->id);
                            },
                        ])
                        ->get();
                    break;
            }
        }

        return view('student.fiche-examen', compact(
            'attempt', 
            'examen', 
            'qcms', 
            'pointillers', 
            'reliers', 
            'codes', 
            'texts', 
            'redactions', 
            'glisserDeposers',
            'fichiers',
            'motsCroisesListe',
            'resume'
        ));
    }
}
