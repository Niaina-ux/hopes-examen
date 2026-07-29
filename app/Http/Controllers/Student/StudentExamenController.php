<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Code;
use App\Models\CodeQuestion;
use App\Models\ExamAttempt;
use App\Models\Examen;
use App\Models\ExamenTypeExercice;
use App\Models\Fichier;
use App\Models\GlisserDeposerReponse;
use App\Models\MotsCroises;
use App\Models\Pointiller;
use App\Models\PointillerEtudiantReponse;
use App\Models\PointillerQuestion;
use App\Models\Qcm;
use App\Models\QcmQuestion;
use App\Models\QcmReponse;
use App\Models\Relier;
use App\Models\RelierQuestion;
use App\Models\RelierReponse;
use App\Models\Student;
use App\Models\StudentExamen;
use App\Models\Text;
use App\Models\TextQuestion;
use App\Models\TypeExercice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentExamenController extends Controller
{
   public function index(string $slug)
    {
        $categorie = Categorie::where('slug', $slug)->firstOrFail();

        $student = Student::where('user_id', Auth::id())->firstOrFail();

        $examen = null;
        $attempt = null;
        $slugRouteFirst = null;
        $studentExamen = null;

        // Manamarina raha mitovy ny categorie an'ny student sy ilay angatahina
        if ($student->categorie_id === $categorie->id) {

            $studentExamen = StudentExamen::where('user_id', Auth::id())
                ->where('termine', false)
                ->whereHas('examen', function ($query) use ($categorie) {
                    $query->where('categorie_id', $categorie->id)
                        ->where('status', 'publie');
                })
                ->orderBy('id', 'asc')
                ->first();

            if ($studentExamen) {
                $examen = Examen::where('id', $studentExamen->examen_id)
                    ->with('typesExercice')
                    ->first();

                if ($examen) {
                    $attempt = ExamAttempt::where('examen_id', $examen->id)
                        ->where('student_id', $student->id)
                        ->where('status', 'en_cours')
                        ->first();

                    if ($attempt && $attempt->date_fin && now()->greaterThanOrEqualTo($attempt->date_fin)) {
                        $attempt->update(['status' => 'termine']);
                        $attempt = null;
                    }

                    $examen_start = ExamenTypeExercice::where('examen_id', $examen->id)
                        ->orderBy('ordre', 'asc')
                        ->first();

                    if ($examen_start) {
                        $slugRouteFirst = TypeExercice::find($examen_start->type_exercice_id);
                    }
                }
            }
        }

        // ✅ Andeha FOANA any amin'ny view, na misy examen na tsia
        return view('student.examen.index', compact(
            'categorie', 'examen', 'attempt', 'slugRouteFirst', 'studentExamen'
        ));
    }
   

    public function start(Examen $examen)
    {
        $student = Student::where('user_id', Auth::id())->firstOrFail();
        $slug = $examen->categorie->slug;
        // Mizaha raha misy tentative "en_cours" efa misy
        $attempt = ExamAttempt::where('examen_id', $examen->id)
            ->where('student_id', $student->id)
            ->where('status', 'en_cours')
            ->latest('id') // na latest('date_debut')
            ->first();
        if (!$attempt) {
            // Maka ny numero_tentative farany, mba hampiakatra 1
            $dernierNumero = ExamAttempt::where('examen_id', $examen->id)
                ->where('student_id', $student->id)
                ->max('numero_tentative');

            $attempt = ExamAttempt::create([
                'student_id'       => $student->id,
                'examen_id'        => $examen->id,
                'numero_tentative' => ($dernierNumero ?? 0) + 1,
                'status'           => 'en_cours',
                'date_debut'       => now(),
                'date_fin'         => now()->addMinutes($examen->duree_minutes ?? 0),
            ]);
        }
        $premierType = $examen->typesExercice()
            ->orderBy('examen_type_exercice.ordre')
            ->first();
        if (!$premierType) {
            return back()->with('error', 'Aucun type d\'exercice défini pour cet examen.');
        }
        return $this->redirectVersPremierElement($examen, $slug, $premierType->slug);
    }


    private function redirectVersPremierElement(Examen $examen, string $slug, string $typeSlug)
    {
        $mapping = config('type_exercices.' . $typeSlug);
        if (!$mapping || !class_exists($mapping['model'])) {
            return back()->with('error', "Type d'exercice « {$typeSlug} » non configuré.");
        }
        $modelClass = $mapping['model'];
        $orderBy    = $mapping['order_by'] ?? 'ordre';
        $premierElement = $modelClass::where('examen_id', $examen->id)
            ->orderBy($orderBy)
            ->first();
        if (!$premierElement) {
            return back()->with('error', "Aucun exercice « {$typeSlug} » disponible pour cet examen.");
        }
        return redirect()->route('examen.' . $typeSlug . '.show', [
            'examen'   => $examen->id,
            'slug'     => $slug,
            $typeSlug  => $premierElement->id,
        ]);
    }
    

    public function terminer(Examen $examen)
    {
        $userId = Auth::id();
        $student = Student::where('user_id', $userId)->firstOrFail();
        $attempt = ExamAttempt::where('examen_id', $examen->id)
            ->where('student_id', $student->id)
            ->latest('id')
            ->firstOrFail();

        if ($attempt->status !== 'termine') {
            $attempt->update([
                'status'   => 'termine',
                'date_fin' => now(),
            ]);
        }

        // $attempt->recalculerScore();
        
        StudentExamen::where('examen_id', $examen->id)
            ->where('user_id', $userId)
            ->update(['termine' => true]);
                   
        $totalPoints = 10;

        return view('student.examen.terminer', compact('examen', 'attempt', 'totalPoints'));
    }
}
