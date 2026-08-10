<?php

namespace App\Traits;

use App\Models\Commentaire;
use App\Models\ExamAttempt;
use App\Models\Examen;
use Illuminate\Database\Eloquent\Collection;

trait LoadsCommentairesExercice
{
    /**
     * Alaina ny TypeExercice mifanaraka amin'ilay slug (qcm, code, text, ...)
     * ao amin'ilay examen, miaraka amin'ny commentaires efa misy ho an'io attempt io.
     *
     * @return array{0: ?TypeExercice, 1: Collection}
     */

    protected function loadCommentairesType(Examen $examen, string $typeSlug, ExamAttempt $attempt): array
    {
        $examen->loadMissing('typesExercice');
        $type = $examen->typesExercice->firstWhere('slug', $typeSlug);

        $comment = $type
            ? $type->commentaires()->where('exam_attempt_id', $attempt->id)->first()  // ✅ first(), tsy get()
            : null;

        return [$type, $comment];
    }
}
