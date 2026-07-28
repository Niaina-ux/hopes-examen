<?php

namespace App\Http\Controllers\Student\Concerns;

use App\Models\Examen;
use App\Models\ExamAttempt;
use App\Models\Student;
use App\Models\TypeExercice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait HandlesExamenFlow
{
    /**
     * Mijery raha misy type_exercice manaraka araka ny ordre,
     * na mamarana ny examen raha lany daholo.
     */
    private function passerAuTypeExerciceSuivant(Examen $examen, string $slug, string $typeActuelSlug)
    {
        $typeActuel = TypeExercice::where('slug', $typeActuelSlug)->first();

        if (!$typeActuel) {
            return $this->terminerExamen($examen);
        }

        $ordreActuel = DB::table('examen_type_exercice')
            ->where('examen_id', $examen->id)
            ->where('type_exercice_id', $typeActuel->id)
            ->value('ordre');

        $typeSuivantId = DB::table('examen_type_exercice')
            ->where('examen_id', $examen->id)
            ->where('ordre', '>', $ordreActuel)
            ->orderBy('ordre')
            ->value('type_exercice_id');

        if ($typeSuivantId) {
            $typeSuivant = TypeExercice::find($typeSuivantId);
            return $this->redirectVersTypeExercice($examen, $slug, $typeSuivant);
        }

        return $this->terminerExamen($examen);
    }

    /**
     * Mamorona ny redirection mifanaraka amin'ny type_exercice tsirairay,
     * satria samy manana table sy id manokana (Qcm, Pointiller, Relier, sns).
     */
    private function redirectVersTypeExercice(Examen $examen, string $slug, TypeExercice $type)
    {
        $mapping = config('type_exercices.' . $type->slug);

        if (!$mapping || !class_exists($mapping['model'])) {
            return $this->passerAuTypeExerciceSuivant($examen, $slug, $type->slug);
        }

        $modelClass = $mapping['model'];
        $orderBy    = $mapping['order_by'] ?? 'ordre';

        $premierElement = $modelClass::where('examen_id', $examen->id)
            ->orderBy($orderBy)
            ->first();

        if (!$premierElement) {
            return $this->passerAuTypeExerciceSuivant($examen, $slug, $type->slug);
        }

        return redirect()->route('examen.' . $type->slug . '.show', [
            'examen'    => $examen->id,
            'slug'      => $slug,
            $type->slug => $premierElement->id,
        ]);
    }

    /**
     * Mamarana tanteraka ny examen ho an'ity attempt ity.
     */
    private function terminerExamen(Examen $examen)
    {
        $student = Student::where('user_id', Auth::id())->firstOrFail();

        $attempt = ExamAttempt::where('examen_id', $examen->id)
            ->where('student_id', $student->id)
            ->where('status', 'en_cours')
            ->latest('id')
            ->first();

        if ($attempt) {
            $attempt->update(['status' => 'termine', 'date_fin' => now()]);
        }

        return redirect()
            ->route('student.examen.terminer', $examen->id)
            ->with('success', 'Examen terminé avec succès.');
    }
}