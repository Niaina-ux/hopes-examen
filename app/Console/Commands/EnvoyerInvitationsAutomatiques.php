<?php

namespace App\Console\Commands;

use App\Mail\InvitationExamenMail;
use App\Models\EmailLog;
use App\Models\StudentExamen;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;

#[Signature('app:envoyer-invitations-automatiques')]
#[Description('Command description')]
class EnvoyerInvitationsAutomatiques extends Command
{
    protected $signature = 'invitations:envoyer-automatique';
    protected $description = "Envoie automatiquement l'invitation aux étudiants dont l'examen est prévu dans 5 jours";

    public function handle(): void
    {
        $dateCible = now()->addDays(1)->toDateString();

        // ✅ Uniquement les examens dont la date tombe exactement dans 5 jours (donc pas encore passée)
        $studentExamens = StudentExamen::whereDate('date_examen', $dateCible)
            ->with(['user', 'examen'])
            ->get();

        $envoyes = 0;

        foreach ($studentExamens as $se) {
            $student = $se->user;
            $examen = $se->examen;

            if (!$student || !$examen) {
                continue;
            }

            // ✅ Évite les doublons : si déjà envoyé (manuel ou automatique) pour cet examen à cet étudiant
            $dejaEnvoye = EmailLog::where('user_id', $student->id)
                ->where('examen_id', $examen->id)
                ->where('type', 'invitation_examen')
                ->exists();

            if ($dejaEnvoye) {
                continue;
            }

            try {
                $motDePasse = $student->password_affiche
                    ? Crypt::decrypt($student->password_affiche)
                    : null;
            } catch (\Exception $e) {
                $motDePasse = null;
            }

            Mail::to($student->email)->queue(new InvitationExamenMail(
                $student,
                $examen,
                $se->date_examen,
                $motDePasse,
                false
            ));

            EmailLog::create([
                'user_id' => $student->id,
                'type' => 'invitation_examen',
                'examen_id' => $examen->id,
                'sujet' => "Invitation à l'examen : {$examen->titre}",
            ]);

            $envoyes++;
        }

        $this->info("{$envoyes} invitation(s) automatique(s) envoyée(s).");
    }
}
