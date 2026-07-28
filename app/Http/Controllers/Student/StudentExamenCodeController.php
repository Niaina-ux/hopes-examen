<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Student\Concerns\HandlesExamenFlow;
use App\Models\Code;
use App\Models\CodeReponse;
use App\Models\ExamAttempt;
use App\Models\Examen;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentExamenCodeController extends Controller
{
    use HandlesExamenFlow;

    public function show(Request $request, Examen $examen, string $slug, Code $code)
    {
        $questions = $code->codeQuestions()->orderBy('ordre')->get();

        if ($questions->isEmpty()) {
            abort(404, 'Aucune question disponible.');
        }

        $totalPoints = $questions->sum('points');

        // Filaharan'ity code_ ity ao amin'ny examen
        $tousLesCode = Code::where('examen_id', $examen->id)
            ->orderBy('ordre')
            ->get();

        $index = $tousLesCode->search(fn($c) => $c->id === $code->id);
        $index = $index === false ? 0 : $index;
        $total = $tousLesCode->count();

        // Alaina ny valiny efa nosoratan'ny mpianatra teo aloha (raha nisy), mba ho voatahiry ny code
        $studentId = Auth::id();
        $student = Student::where('user_id', $studentId)->first();

        $attempt = $student
            ? ExamAttempt::where('examen_id', $examen->id)
                ->where('student_id', $student->id)
                ->where('status', 'en_cours')
                ->latest('id')
                ->first()
            : null;

        $reponsesExistantes = [];
        if ($attempt) {
            $reponsesExistantes = CodeReponse::where('exam_attempt_id', $attempt->id)
                ->whereIn('code_question_id', $questions->pluck('id'))
                ->pluck('code_soumis', 'code_question_id')
                ->toArray();
        }

        return view('student.examen.code.show', compact(
            'examen', 'slug', 'code', 'questions', 'totalPoints', 'index', 'total', 'reponsesExistantes'
        ));
    }

    public function store(Request $request, Examen $examen, string $slug, Code $code)
    {
        $validated = $request->validate([
            'codes'   => ['required', 'array'],
            'codes.*' => ['nullable', 'string'],
        ]);

        $studentId = Auth::id();
        $student = Student::where('user_id', $studentId)->firstOrFail();

        $attempt = ExamAttempt::where('examen_id', $examen->id)
            ->where('student_id', $student->id)
            ->where('status', 'en_cours')
            ->latest('id')
            ->firstOrFail();

        $questions = $code->codeQuestions()->get();

        foreach ($questions as $question) {
            $codeSoumis = $validated['codes'][$question->id] ?? '';

            // Esory ny valiny taloha ho an'ity question sy attempt ity
            CodeReponse::where('code_question_id', $question->id)
                ->where('student_id', $studentId)
                ->where('exam_attempt_id', $attempt->id)
                ->delete();

            CodeReponse::create([
                'code_question_id' => $question->id,
                'exam_attempt_id'      => $attempt->id,
                'student_id'           => $studentId,
                'code_soumis'          => $codeSoumis,
                'points_obtenus'       => null, // ✅ null = mbola tsy notsimbina ny prof
                'commentaire_prof'     => null,
                'est_corrige'          => false,
            ]);
        }

        // Tsy mikajy score eto satria "correction manuelle" — averina any aoriana rehefa vita ny fanitsian'ny prof
        // (raha te-hikajy ny score azo hatreto ihany, azo ampiana $attempt->recalculerScore() ihany koa,
        //  fa hisy "points_obtenus = null" tsy hisy dikany amin'ny sum() mandra-pahavitan'ny fanitsiana)

        $codeSuivant = Code::where('examen_id', $examen->id)
            ->where('ordre', '>', $code->ordre)
            ->orderBy('ordre')
            ->first();

        if ($codeSuivant) {
            return redirect()->route('examen.code.show', [
                'examen' => $examen->id,
                'slug'   => $slug,
                'code'   => $codeSuivant->id,
            ]);
        }

        return $this->passerAuTypeExerciceSuivant($examen, $slug, 'code');
    }
}
