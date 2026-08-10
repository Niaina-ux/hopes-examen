<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TextReponse extends Model
{
    use HasFactory;

    protected $table = 'text_reponses';

    protected $fillable = [
        'text_question_id',
        'exam_attempt_id',
        'student_id',
        'reponse_texte',
        'reponse_annotee',
        'submitted_at',
        'note_obtenue',
        'commentaire_prof',
    ];

    protected $casts = [
        'submitted_at'  => 'datetime',
        'note_obtenue'  => 'decimal:2',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(TextQuestion::class, 'text_question_id');
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class, 'exam_attempt_id');
    }
}
