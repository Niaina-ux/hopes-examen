<?php

namespace App\Http\Middleware;

use App\Models\ExamAttempt;
use App\Models\Student;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckExamenEnCours
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $examen = $request->route('examen');

        $student = Student::where('user_id', Auth::id())->first();

        if (!$student) {
            abort(403, 'Profil étudiant introuvable.');
        }

        $attempt = ExamAttempt::where('examen_id', $examen->id)
            ->where('student_id', $student->id)
            ->where('status', 'en_cours')
            ->latest('numero_tentative')
            ->first();

        if (!$attempt || !$attempt->date_debut) {
            abort(403, 'Vous devez d\'abord commencer cet examen.');
        }

        $secondesRestantes = $attempt->date_fin->getTimestamp() - time();

        if ($secondesRestantes <= 0) {
            $attempt->update(['status' => 'termine', 'date_fin' => now()]);
            abort(403, 'Le temps est écoulé pour cet examen.');
        }

        view()->share('secondesRestantes', $secondesRestantes);
        view()->share('examAttempt', $attempt);

        return $next($request);
    }
}
