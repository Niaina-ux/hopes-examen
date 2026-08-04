<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Student\Concerns\HandlesExamenFlow;
use App\Models\ExamAttempt;
use App\Models\Examen;
use App\Models\Fichier;
use App\Models\FichierReponse;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentExamenFichierController extends Controller
{
    use HandlesExamenFlow;

    public function show(Request $request, Examen $examen, string $slug, Fichier $fichier)
    {
        $questions = $fichier->fichierQuestions()->orderBy('ordre')->get();

        if ($questions->isEmpty()) {
            abort(404, 'Aucun devoir disponible.');
        }

        $studentId = Auth::id();
        $student = Student::where('user_id', $studentId)->firstOrFail();

        // ✅ Alaina ny ExamAttempt "en_cours" ho an'ity examen sy student ity
        $attempt = ExamAttempt::where('examen_id', $examen->id)
            ->where('student_id', $student->id)
            ->where('status', 'en_cours')
            ->latest('id')
            ->firstOrFail();

        // ✅ Mifototra amin'ny exam_attempt_id, mitovy amin'ny qcm/pointiller/relier/code
        $reponsesExistantes = FichierReponse::whereIn('fichier_question_id', $questions->pluck('id'))
            ->where('exam_attempt_id', $attempt->id)
            ->get()
            ->keyBy('fichier_question_id');

        $totalPoints = $questions->sum('points');

        // ✅ Mikajy ny filaharan'ity fichierWeb ity ao amin'ny examen (index/total)
        $tousLesFichier = Fichier::where('examen_id', $examen->id)
            ->orderBy('ordre')
            ->get();

        $index = $tousLesFichier->search(fn($f) => $f->id === $fichier->id);
        $index = $index === false ? 0 : $index;
        $total = $tousLesFichier->count();

        return view('student.examen.downloadUpload.show', compact(
            'examen', 'slug', 'fichier', 'questions', 'totalPoints', 'reponsesExistantes', 'index', 'total'
        ));
    }

    public function store(Request $request, Examen $examen, string $slug, Fichier $fichier)
    {
        $questions = $fichier->fichierQuestions()->get();

        $rules = [];
        foreach ($questions as $question) {
            $rules["fichiers.{$question->id}"] = ['nullable', 'file', 'mimes:pdf,doc,docx,zip,rar', 'max:10240'];
        }

        $validated = $request->validate($rules, [
            'fichiers.*.mimes' => 'Le fichier doit être au format pdf, doc, docx, zip ou rar.',
            'fichiers.*.max' => 'Le fichier ne doit pas dépasser 10 Mo.',
        ]);

        $student = Student::where('user_id', Auth::id())->firstOrFail();

        $student = Student::where('user_id', Auth::id())
            ->firstOrFail();

        $attempt = ExamAttempt::where('examen_id', $examen->id)
            ->where('student_id', $student->id)
            ->where('status', 'en_cours')
            ->latest('id')
            ->firstOrFail();

        foreach ($questions as $question) {
            if (!$request->hasFile("fichiers.{$question->id}")) {
                continue; // aza ovaina raha tsy misy fichier vaovao alefa
            }

            $file = $request->file("fichiers.{$question->id}");
            $fileName = time() . '_' . $student->id . '_' . $file->getClientOriginalName();
            $file->move(public_path('fichiers/etudiants'), $fileName);

            FichierReponse::updateOrCreate(
                [
                    'fichier_question_id' => $question->id,
                    'exam_attempt_id'     => $attempt->id,
                    'student_id'          => $student->user_id, 
                ],
                [
                    'fichier_etudiant' => $fileName,
                    'points_obtenus'   => null,
                    'commentaire_prof' => null,
                    'est_corrige'      => false,
                ]
        );
        }

        // Devoir manaraka ao anaty examen iray ihany
        $fichierSuivant = Fichier::where('examen_id', $examen->id)
            ->where('id', '>', $fichier->id)
            ->orderBy('id')
            ->first();

        if ($fichierSuivant) {
            return redirect()->route('examen.fichier.show', [
                'examen' => $examen->id,
                'slug' => $slug,
                'fichier' => $fichierSuivant->id,
            ]);
        }

        return $this->passerAuTypeExerciceSuivant($examen, $slug, 'download-upload');
    }

}
