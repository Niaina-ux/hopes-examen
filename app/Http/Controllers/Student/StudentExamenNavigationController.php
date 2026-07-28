<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Examen;
use App\Models\TypeExercice;

class StudentExamenNavigationController extends Controller
{
    public function redirectToFirstExercice(Examen $examen)
    {
        $premierType = $examen->typesExercice()->first();

        if (!$premierType) {
            abort(404, 'Aucun type d\'exercice configuré pour cet examen.');
        }

        return $this->redirectToTypeExercice($examen, $premierType);
    }


    public function next(Examen $examen, TypeExercice $currentType)
    {
        $currentPivotOrdre = $examen->typesExercice()
            ->where('types_exercice.id', $currentType->id)
            ->first()
            ->pivot
            ->ordre;

        $suivant = $examen->typesExercice()
            ->wherePivot('ordre', '>', $currentPivotOrdre)
            ->orderBy('examen_type_exercice.ordre')
            ->first();

        if (!$suivant) {
            return redirect()->route('student.examen.termine', $examen->id);
        }

        return $this->redirectToTypeExercice($examen, $suivant);
    }


    private function redirectToTypeExercice(Examen $examen, TypeExercice $type)
    {
        return match ($type->slug) {
            'qcm' => $this->goToQcm($examen),
            'pointiller' => $this->goToPointiller($examen),
            'relier' => $this->goToRelier($examen),
            'code' => $this->goToCode($examen),
            default => abort(404, 'Type d\'exercice non pris en charge : ' . $type->slug),
        };
    }


    private function goToQcm(Examen $examen)
    {
        $qcmWeb = $examen->qcmWebs()->first();
        if (!$qcmWeb) abort(404, 'Aucun QCM disponible.');

        return redirect()->route('student.examen.web.qcm', [$examen->id, $qcmWeb->id]);
    }


    private function goToPointiller(Examen $examen)
    {
        $pointillerWeb = $examen->pointillerWebs()->first();
        if (!$pointillerWeb) abort(404, 'Aucun exercice disponible.');

        return redirect()->route('student.examen.pointiller', [$examen->id, $pointillerWeb->id]);
    }


    private function goToRelier(Examen $examen)
    {
        $relierWeb = $examen->relierWebs()->first();
        if (!$relierWeb) abort(404, 'Aucun exercice disponible.');

        return redirect()->route('student.examen.relier', [$examen->id, $relierWeb->id]);
    }


    private function goToCode(Examen $examen)
    {
        $codeWeb = $examen->codeWebs()->first();
        if (!$codeWeb) abort(404, 'Aucun exercice disponible.');

        return redirect()->route('student.examen.code', [$examen->id, $codeWeb->id]);
    }
}
