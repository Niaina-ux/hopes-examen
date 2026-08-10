<?php

namespace App\Traits;

use App\Models\Examen;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

trait ResolvesExamenEtudiant
{
    protected function resolveExamenEtudiant(string $slug, string $examenId, string $studentId): array|RedirectResponse
    {
        $examen = Examen::find($examenId);
        if (!$examen) {
            return redirect()->route('prof.examen.show', $slug)
                ->with('error', "Il y a un problème dans l'URL !");
        }

        $student = User::find($studentId);
        if (!$student) {
            return redirect()->route('prof.examen.studentswithexamen', [$slug, $examen->id])
                ->with('error', "Étudiant n'existe pas !");
        }

        $etudiant = $student->student;
        if (!$etudiant) {
            return redirect()->route('prof.examen.studentswithexamen', [$slug, $examen->id])
                ->with('error', "Cet étudiant est introuvable.");
        }

        return [$examen, $student, $etudiant];
    }
}
