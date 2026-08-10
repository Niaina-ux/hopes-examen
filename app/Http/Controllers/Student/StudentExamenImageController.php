<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Student\Concerns\HandlesExamenFlow;
use App\Models\ExamAttempt;
use App\Models\Examen;
use App\Models\ImageExercice;
use App\Models\ImageExerciceReponse;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentExamenImageController extends Controller
{
    use HandlesExamenFlow;

    public function show(Request $request, Examen $examen, string $slug, ImageExercice $image)
    {
        $questions = $image->questions()->orderBy('ordre')->get();

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
        $reponsesExistantes = ImageExerciceReponse::whereIn('image_exercice_question_id', $questions->pluck('id'))
            ->where('exam_attempt_id', $attempt->id)
            ->get()
            ->keyBy('image_exercice_question_id');

        $totalPoints = $questions->sum('points');

        // ✅ Mikajy ny filaharan'ity fichierWeb ity ao amin'ny examen (index/total)
        $tousLesImage = ImageExercice::where('examen_id', $examen->id)
            ->orderBy('ordre')
            ->get();

        $index = $tousLesImage->search(fn($f) => $f->id === $image->id);
        $index = $index === false ? 0 : $index;
        $total = $tousLesImage->count();

        return view('student.examen.image-exericice.show', compact(
            'examen', 'slug', 'image', 'questions', 'totalPoints', 'reponsesExistantes', 'index', 'total'
        ));
    }

    public function store(Request $request, Examen $examen, string $slug, ImageExercice $image)
    {
        $questions = $image->questions()->get();

        $rules = [];
        foreach ($questions as $question) {
            $rules["images.{$question->id}"] = [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ];
        }

        $request->validate($rules);

        $student = Student::where('user_id', Auth::id())->firstOrFail();

        $attempt = ExamAttempt::where('examen_id', $examen->id)
            ->where('student_id', $student->id)
            ->where('status', 'en_cours')
            ->latest('id')
            ->firstOrFail();

        foreach ($questions as $question) {

            if (!$request->hasFile("images.{$question->id}")) {
                continue;
            }

            $imageFile = $request->file("images.{$question->id}");

            $imageName = time().'_'.$student->id.'_'.$imageFile->getClientOriginalName();

            $imageFile->move(public_path('images/image_reponses'), $imageName);

            ImageExerciceReponse::updateOrCreate(
                [
                    'image_exercice_question_id' => $question->id,
                    'exam_attempt_id'            => $attempt->id,
                    'student_id'                 => $student->user_id,
                ],
                [
                    'image_soumise'    => $imageName,
                    'points_obtenus'   => null,
                    'commentaire_prof' => null,
                    'est_corrige'      => false,
                ]
            );
        }

        $imageSuivant = ImageExercice::where('examen_id', $examen->id)
            ->where('ordre', '>', $image->ordre)
            ->orderBy('ordre')
            ->first();

        if ($imageSuivant) {
            return redirect()->route('examen.image.show', [
                'examen' => $examen,
                'slug'   => $slug,
                'image'  => $imageSuivant,
            ]);
        }

        return $this->passerAuTypeExerciceSuivant($examen, $slug, 'image');
    }
}
