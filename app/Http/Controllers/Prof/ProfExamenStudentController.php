<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Code;
use App\Models\ExamAttempt;
use App\Models\Examen;
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
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfExamenStudentController extends Controller
{
    public function studentswithexamen(Request $request, string $slug, Examen $examen)
    {
        $tousLesStudentExamen = StudentExamen::where('examen_id', $examen->id)
            ->whereNotNull('date_examen')
            ->get();

        $datesDisponibles = $tousLesStudentExamen
            ->map(fn($se) => $se->date_examen->format('Y-m-d'))
            ->unique()
            ->sort()
            ->values();

        //  Manisa ny mpianatra isaky ny daty
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

        return view('prof.student.studentswithexamen', compact(
            'slug', 'examen', 'studentwithexam', 'datesDisponibles', 'dateSelectionnee',
            'students', 'attempts', 'nombreParDate'
        ));
    }

    public function examenwherestudent(string $slug, Examen $examen, int $student_id)
    {
        $user = Auth::user();
        $prof = $user->prof;

        if (!$prof) {
            abort(403, 'Accès réservé aux professeurs.');
        }

        $profCategorie = Categorie::findOrFail($prof->categorie_id);

        $student = User::findOrFail($student_id);
        $etudiant = $student->student;

        if (!$etudiant) {
            return redirect()->route('prof.page.notfound')->with('error', 'Profil étudiant introuvable.');
        }

        if ($slug !== $profCategorie->slug) {
            return redirect()->route('prof.page.notfound')->with('error', 'Vous n\'êtes pas autorisé à consulter cette catégorie.');
        }

        if ($examen->categorie->slug !== $slug) {
            return redirect()->route('prof.examen.studentswithexamen', [$slug, $examen->id])->with('error', 'Examen introuvable pour cette catégorie.');
        }

        if ($etudiant->categorie_id !== $examen->categorie_id) {
            return redirect()->route('prof.examen.studentswithexamen', [$slug, $examen->id])->with('error', 'Cet étudiant n\'appartient pas à la catégorie de cet examen.');
        }

        $estAssigne = StudentExamen::where('examen_id', $examen->id)->where('user_id', $student->id)->exists();
        if (!$estAssigne) {
            return redirect()->route('prof.examen.studentswithexamen', [$slug, $examen->id])->with('error', 'Cet étudiant n\'est pas assigné à cet examen.');
        }

        $examen->load('typesExercice');
        $premierType = $examen->typesExercice->sortByDesc('ordre')->first();

        $attempt = ExamAttempt::where('examen_id', $examen->id)
            ->where('student_id', $etudiant->id)
            ->first();

        // ✅ Bulletin de note — calculé seulement si l'examen a une attempt terminée
        $resumeParType = [];
        $totalPointsGlobalObtenus = 0;
        $totalNoteGlobal = 0;
        $toutEstCorrige = true;

        if ($attempt && $attempt->status !== 'en_cours') {
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
                            $obtenus = \App\Models\PointillerEtudiantReponse::whereIn(
                                'pointiller_reponse_id',
                                $pointillers->pluck('pointillerQuestions')->flatten()->pluck('reponses')->flatten()->pluck('id')
                            )->where('exam_attempt_id', $attempt->id)->sum('points_obtenus');
                            $resumeParType['pointiller'] = ['nom' => 'Pointiller', 'obtenus' => $obtenus, 'total' => $pointillers->sum('note_totale')];
                        }
                        break;

                    case 'relier':
                        $reliers = Relier::where('examen_id', $examen->id)->with('relierQuestions.paires')->get();
                        if ($reliers->isNotEmpty()) {
                            $obtenus = \App\Models\RelierReponse::whereIn(
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
                            $obtenus = $reponses->sum('points_obtenus');
                            $estCorrige = $reponses->isNotEmpty() && $reponses->every(fn($r) => $r->points_obtenus !== null);
                            $resumeParType['code'] = ['nom' => 'Code', 'obtenus' => $obtenus, 'total' => $codes->sum('note_totale'), 'corrige' => $estCorrige];
                        }
                        break;

                    case 'text':
                        $texts = Text::where('examen_id', $examen->id)
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
                        $redactions = Redaction::where('examen_id', $examen->id)
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
                            $obtenus = $reponses->sum('points_obtenus');
                            $estCorrige = $reponses->isNotEmpty() && $reponses->every(fn($r) => $r->points_obtenus !== null);
                            $resumeParType['fichier'] = ['nom' => 'Devoir à rendre', 'obtenus' => $obtenus, 'total' => $fichiers->sum('note_totale'), 'corrige' => $estCorrige];
                        }
                        break;

                    case 'image':
                        $images = ImageExercice::where('examen_id', $examen->id)
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
                } else {
                    $toutEstCorrige = false;
                }
            }
        }

        return view('prof.student.examenwherestudent', compact(
            'slug', 'examen', 'student', 'premierType', 'attempt', 'etudiant',
            'resumeParType', 'totalPointsGlobalObtenus', 'totalNoteGlobal', 'toutEstCorrige'
        ));
    }

}
