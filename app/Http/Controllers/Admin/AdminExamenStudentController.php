<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailLog;
use App\Models\ExamAttempt;
use App\Models\Examen;
use App\Models\Student;
use App\Models\StudentExamen;
use App\Models\User;
use App\Traits\CalculeStatistiquesExamen;
use Illuminate\Http\Request;

class AdminExamenStudentController extends Controller
{
    use CalculeStatistiquesExamen;
    
    public function show(Request $request, string $slug, Examen $examen)
    {
        $tousLesStudentExamen = StudentExamen::where('examen_id', $examen->id)
            ->whereNotNull('date_examen')
            ->get();

        $datesDisponibles = $tousLesStudentExamen
            ->map(fn($se) => $se->date_examen->format('Y-m-d'))
            ->unique()
            ->sort()
            ->values();

        $nombreParDate = $tousLesStudentExamen
            ->groupBy(fn($se) => $se->date_examen->format('Y-m-d'))
            ->map(fn($group) => $group->count());

        $dateSelectionnee = $request->query('date', $datesDisponibles->last());

        $studentwithexam = StudentExamen::where('examen_id', $examen->id)
            ->when($dateSelectionnee, function ($query) use ($dateSelectionnee) {
                $query->whereDate('date_examen', $dateSelectionnee);
            })
            ->with('user.student')
            ->get();

        $userIds = $studentwithexam->pluck('user_id');
        $students = Student::whereIn('user_id', $userIds)->pluck('id', 'user_id');

        $attempts = ExamAttempt::where('examen_id', $examen->id)
            ->where('numero_tentative', 1)
            ->whereIn('student_id', $students->values())
            ->get()
            ->keyBy('student_id');

        $emailsEnvoyes = EmailLog::where('examen_id', $examen->id)
            ->where('type', 'invitation_examen')
            ->pluck('user_id')
            ->toArray();


        return view('admin.examen.examen-student.show', compact(
            'slug', 'examen', 'studentwithexam', 'datesDisponibles',
            'dateSelectionnee', 'students', 'attempts', 'nombreParDate',
            'emailsEnvoyes'
        ));
    }

     
    public function create(string $slug, Examen $examen)
    {
        $userIdsDejaInvites = StudentExamen::where(
            'examen_id',
            $examen->id
        )
            ->pluck('user_id')
            ->toArray();

        $students = Student::where(
            'categorie_id',
            $examen->categorie_id
        )
            ->whereNotIn(
                'user_id',
                $userIdsDejaInvites
            )
            ->with('user')
            ->get();

        return view(
            'admin.examen.examen-student.create',
            compact(
                'slug',
                'examen',
                'students'
            )
        );
    }

    public function store(Request $request, string $slug, Examen $examen)
    {
        $validated = $request->validate([
            'student_user_ids' => ['required', 'array', 'min:1'],
            'student_user_ids.*' => ['exists:users,id'],
        ], [
            'student_user_ids.required' =>
                'Veuillez sélectionner au moins un étudiant.',
        ]);

        if (!$examen->date_examen) {
            return back()->withErrors([
                'student_user_ids' =>
                    "La date de l'examen n'est pas encore définie.",
            ])->withInput();
        }

        $studentsValides = Student::where(
            'categorie_id',
            $examen->categorie_id
        )
            ->whereIn(
                'user_id',
                $validated['student_user_ids']
            )
            ->pluck('user_id');

        if ($studentsValides->isEmpty()) {
            return back()->withErrors([
                'student_user_ids' =>
                    'Aucun étudiant valide sélectionné pour cette catégorie.',
            ])->withInput();
        }

        foreach ($studentsValides as $userId) {
            StudentExamen::updateOrCreate(
                [
                    'examen_id' => $examen->id,
                    'user_id' => $userId,
                ],
                [
                    'date_examen' => $examen->date_examen,
                    'termine' => false,
                ]
            );
        }

        return redirect()
            ->route(
                'admin.examen.student.create',
                [$slug, $examen->id]
            )
            ->with(
                'success',
                'Étudiants assignés à l\'examen avec succès.'
            );
    }

    
    public function destroy(string $slug, Examen $examen, StudentExamen $studentExamen)
    {
        $studentExamen->delete();

        return redirect()
            ->back()
            ->with('success', 'Étudiant retiré de l\'examen.');
    }


    

    public function examenwherestudent(string $slug, int $examenId, int $studentId)
    {
        $examen = Examen::find($examenId);
        if (!$examen) {
            return redirect()
                ->route('admin.examen.show', $slug)
                ->with('error', "Il y a un problème dans l'URL !");
        }

        $student = User::find($studentId);
        if (!$student) {
            return redirect()
                ->route('admin.student.index');
        }

        $etudiant = $student->student;
        if (!$etudiant) {
            return redirect()
                ->route('admin.student.index');
        }

        $bulletinEnvoye = EmailLog::where('user_id', $student->id)
            ->where('examen_id', $examen->id)
            ->where('type', 'bulletin_examen')
            ->exists();

        $examen->load('typesExercice');
        $premierType = $examen->typesExercice->sortByDesc('ordre')->first();

        $attempt = ExamAttempt::where('examen_id', $examen->id)
            ->where('student_id', $etudiant->id)
            ->where('numero_tentative', 1)
            ->first();

        
        $resumeParType = [];
        $totalPointsGlobalObtenus = 0;
        $totalNoteGlobal = 0;
        $toutEstCorrige = true;

        if ($attempt && $attempt->status === 'corrige') {
            $typesCorrectionManuelle = ['code', 'text', 'redaction', 'fichier', 'image'];

            foreach ($examen->typesExercice as $type) {
                switch ($type->slug) {
                    case 'qcm':
                        $qcms = \App\Models\Qcm::where('examen_id', $examen->id)
                            ->with(['qcmQuestions.qcmReponsesEtudiants' => fn($q) => $q->where('exam_attempt_id', $attempt->id)])
                            ->get();
                        if ($qcms->isNotEmpty()) {
                            $obtenus = $qcms->flatMap(fn($q) => $q->qcmQuestions)->flatMap(fn($q) => $q->qcmReponsesEtudiants)->sum('points_obtenus');
                            $resumeParType['qcm'] = ['nom' => 'QCM', 'obtenus' => $obtenus, 'total' => $qcms->sum('note_totale')];
                        }
                        break;

                    case 'pointiller':
                        $pointillers = \App\Models\Pointiller::where('examen_id', $examen->id)->with('pointillerQuestions.reponses')->get();
                        if ($pointillers->isNotEmpty()) {
                            $obtenus = \App\Models\PointillerEtudiantReponse::whereIn(
                                'pointiller_reponse_id',
                                $pointillers->pluck('pointillerQuestions')->flatten()->pluck('reponses')->flatten()->pluck('id')
                            )->where('exam_attempt_id', $attempt->id)->sum('points_obtenus');
                            $resumeParType['pointiller'] = ['nom' => 'Pointiller', 'obtenus' => $obtenus, 'total' => $pointillers->sum('note_totale')];
                        }
                        break;

                    case 'relier':
                        $reliers = \App\Models\Relier::where('examen_id', $examen->id)->with('relierQuestions.paires')->get();
                        if ($reliers->isNotEmpty()) {
                            $obtenus = \App\Models\RelierReponse::whereIn(
                                'relier_paire_id',
                                $reliers->pluck('relierQuestions')->flatten()->pluck('paires')->flatten()->pluck('id')
                            )->where('exam_attempt_id', $attempt->id)->sum('points_obtenus');
                            $resumeParType['relier'] = ['nom' => 'Relier par flèche', 'obtenus' => $obtenus, 'total' => $reliers->sum('note_totale')];
                        }
                        break;

                    case 'code':
                        $codes = \App\Models\Code::where('examen_id', $examen->id)
                            ->with(['codeQuestions.reponses' => fn($q) => $q->where('exam_attempt_id', $attempt->id)])
                            ->get();
                        if ($codes->isNotEmpty()) {
                            $reponses = $codes->flatMap(fn($c) => $c->codeQuestions)->flatMap(fn($q) => $q->reponses);
                            $obtenus = $reponses->sum('points_obtenus');
                            $estCorrige = $reponses->isNotEmpty() && $reponses->every(fn($r) => $r->points_obtenus !== null);
                            $resumeParType['code'] = ['nom' => 'Code', 'obtenus' => $obtenus, 'total' => $codes->sum('note_totale'), 'corrige' => $estCorrige];
                        }
                        break;

                    case 'text':
                        $texts = \App\Models\Text::where('examen_id', $examen->id)
                            ->with(['textQuestions.reponses' => fn($q) => $q->where('exam_attempt_id', $attempt->id)])
                            ->get();
                        if ($texts->isNotEmpty()) {
                            $reponses = $texts->flatMap(fn($t) => $t->textQuestions)->flatMap(fn($q) => $q->reponses);
                            $obtenus = $reponses->sum('note_obtenue');
                            $estCorrige = $reponses->isNotEmpty() && $reponses->every(fn($r) => $r->note_obtenue !== null);
                            $resumeParType['text'] = ['nom' => 'Compréhension de texte', 'obtenus' => $obtenus, 'total' => $texts->sum('note_totale'), 'corrige' => $estCorrige];
                        }
                        break;

                    case 'redaction':
                        $redactions = \App\Models\Redaction::where('examen_id', $examen->id)
                            ->with(['reponses' => fn($q) => $q->where('exam_attempt_id', $attempt->id)])
                            ->get();
                        if ($redactions->isNotEmpty()) {
                            $reponses = $redactions->flatMap(fn($r) => $r->reponses);
                            $obtenus = $reponses->sum('note_obtenue');
                            $estCorrige = $reponses->isNotEmpty() && $reponses->every(fn($r) => $r->note_obtenue !== null);
                            $resumeParType['redaction'] = ['nom' => 'Rédaction', 'obtenus' => $obtenus, 'total' => $redactions->sum('note_totale'), 'corrige' => $estCorrige];
                        }
                        break;

                    case 'glisserdeposer':
                        $glisserDeposers = \App\Models\GlisserDeposer::where('examen_id', $examen->id)
                            ->with(['questions.items.reponses' => fn($q) => $q->where('exam_attempt_id', $attempt->id)])
                            ->get();
                        if ($glisserDeposers->isNotEmpty()) {
                            $obtenus = $glisserDeposers->flatMap(fn($g) => $g->questions)->flatMap(fn($q) => $q->items)->flatMap(fn($i) => $i->reponses)->sum('points_obtenus');
                            $resumeParType['glisserdeposer'] = ['nom' => 'Glisser-déposer', 'obtenus' => $obtenus, 'total' => $glisserDeposers->sum('note_totale')];
                        }
                        break;

                    case 'fichier':
                        $fichiers = \App\Models\Fichier::where('examen_id', $examen->id)
                            ->with(['fichierQuestions.reponses' => fn($q) => $q->where('exam_attempt_id', $attempt->id)])
                            ->get();
                        if ($fichiers->isNotEmpty()) {
                            $reponses = $fichiers->flatMap(fn($f) => $f->fichierQuestions)->flatMap(fn($q) => $q->reponses);
                            $obtenus = $reponses->sum('points_obtenus');
                            $estCorrige = $reponses->isNotEmpty() && $reponses->every(fn($r) => $r->points_obtenus !== null);
                            $resumeParType['fichier'] = ['nom' => 'Devoir à rendre', 'obtenus' => $obtenus, 'total' => $fichiers->sum('note_totale'), 'corrige' => $estCorrige];
                        }
                        break;

                    case 'image':
                        $images = \App\Models\ImageExercice::where('examen_id', $examen->id)
                            ->with(['questions.reponses' => fn($q) => $q->where('exam_attempt_id', $attempt->id)])
                            ->get();
                        if ($images->isNotEmpty()) {
                            $reponses = $images->flatMap(fn($i) => $i->questions)->flatMap(fn($q) => $q->reponses);
                            $obtenus = $reponses->sum('points_obtenus');
                            $estCorrige = $reponses->isNotEmpty() && $reponses->every(fn($r) => $r->points_obtenus !== null);
                            $resumeParType['image'] = ['nom' => 'Devoir image', 'obtenus' => $obtenus, 'total' => $images->sum('note_totale'), 'corrige' => $estCorrige];
                        }
                        break;

                    case 'motscroises':
                        $motsCroisesListe = \App\Models\MotsCroises::where('examen_id', $examen->id)
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
                } else {
                    $toutEstCorrige = false;
                }
            }
        }

        return view('admin.student.examenwherestudent', compact(
            'slug', 'examen', 'student', 'premierType', 'attempt', 'etudiant',
            'resumeParType', 'totalPointsGlobalObtenus', 'totalNoteGlobal', 'toutEstCorrige',
            'bulletinEnvoye'         
        ));
    }


    
}
