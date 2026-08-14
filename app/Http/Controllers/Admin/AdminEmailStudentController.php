<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\InvitationExamenMail;
use App\Models\EmailLog;
use App\Models\ExamAttempt;
use App\Models\Examen;
use App\Models\StudentExamen;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;

class AdminEmailStudentController extends Controller
{
    
    public function notifierEtudiant(string $slug, int $examenId, int $studentId)
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
                ->route('admin.examen.student.show', [$slug, $examen->id])
                ->with('error', "Étudiant introuvable.");
        }

        $studentExamen = StudentExamen::where('examen_id', $examen->id)
            ->where('user_id', $student->id)
            ->first();

        if (!$studentExamen) {
            return redirect()
                ->route('admin.examen.student.show', [$slug, $examen->id])
                ->with('error', "Cet étudiant n'est pas assigné à cet examen.");
        }

        try {
            $motDePasse = $student->password_affiche
                ? Crypt::decrypt($student->password_affiche)
                : null;
        } catch (\Exception $e) {
            $motDePasse = null;
        }

        // ✅ Détecte si la date est déjà passée
        $enRetard = $studentExamen->date_examen && \Carbon\Carbon::parse($studentExamen->date_examen)->isPast();

        $sujet = $enRetard
            ? "Votre examen est en retard : {$examen->titre}"
            : "Invitation à l'examen : {$examen->titre}";

        Mail::to($student->email)->queue(new InvitationExamenMail(
            $student,
            $examen,
            $studentExamen->date_examen,
            $motDePasse,
            $enRetard
        ));

        EmailLog::create([
            'user_id' => $student->id,
            'type' => 'invitation_examen',
            'examen_id' => $examen->id,
            'sujet' => $sujet,
        ]);

        return back()->with('success', "Email envoyé à {$student->name}.");
    }

    public function notifierGroupe(Request $request, string $slug, int $examenId)
    {
        $examen = Examen::find($examenId);
        if (!$examen) {
            return redirect()
                ->route('admin.examen.show', $slug)
                ->with('error', "Il y a un problème dans l'URL !");
        }

        $validated = $request->validate([
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['integer', 'exists:users,id'],
        ], [
            'student_ids.required' => 'Veuillez sélectionner au moins un étudiant.',
        ]);

        $studentExamens = StudentExamen::where('examen_id', $examen->id)
            ->whereIn('user_id', $validated['student_ids'])
            ->with('user')
            ->get();

        if ($studentExamens->isEmpty()) {
            return back()->with('error', "Aucun étudiant valide sélectionné.");
        }

        $envoyes = 0;
        $ignores = 0;

        foreach ($studentExamens as $se) {
            $student = $se->user;
            if (!$student) {
                continue;
            }

            $aDejaFaitExamen = ExamAttempt::where('examen_id', $examen->id)
                ->where('student_id', $student->student?->id)
                ->exists();

            if ($aDejaFaitExamen) {
                $ignores++;
                continue;
            }

            try {
                $motDePasse = $student->password_affiche
                    ? Crypt::decrypt($student->password_affiche)
                    : null;
            } catch (\Exception $e) {
                $motDePasse = null;
            }

            $enRetard = $se->date_examen && \Carbon\Carbon::parse($se->date_examen)->isPast();

            $sujet = $enRetard
                ? "Votre examen est en retard : {$examen->titre}"
                : "Invitation à l'examen : {$examen->titre}";

            Mail::to($student->email)->queue(new InvitationExamenMail(
                $student,
                $examen,
                $se->date_examen,
                $motDePasse,
                $enRetard
            ));

            EmailLog::create([
                'user_id' => $student->id,
                'type' => 'invitation_examen',
                'examen_id' => $examen->id,
                'sujet' => $sujet,
            ]);

            $envoyes++;
        }

        $message = "{$envoyes} invitation(s) mise(s) en file d'envoi.";
        if ($ignores > 0) {
            $message .= " {$ignores} étudiant(s) ignoré(s) (examen déjà commencé).";
        }

        return redirect()
            ->route('admin.examen.student.show', [$slug, $examen->id])
            ->with('success', $message);
    }
}
