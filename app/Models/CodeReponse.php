<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CodeReponse extends Model
{
    use HasFactory;

    protected $table = 'code_reponses';

    protected $fillable = [
        'code_question_id',
        'exam_attempt_id',
        'student_id',
        'code_soumis',
        'points_obtenus',
        'commentaire_prof',
        'est_corrige',
    ];

    protected $casts = [
        'est_corrige' => 'boolean',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(CodeQuestion::class, 'code_question_id');
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class, 'exam_attempt_id');
    }
}
