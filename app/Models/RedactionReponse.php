<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RedactionReponse extends Model
{
    use HasFactory;

    protected $table = 'redaction_reponses';

    protected $fillable = [
        'redaction_id',
        'exam_attempt_id',
        'student_id',
        'reponse_texte',
        'nombre_mots',
        'submitted_at',
        'note_obtenue',
        'commentaire_prof',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'note_obtenue' => 'decimal:2',
    ];

    public function redaction(): BelongsTo
    {
        return $this->belongsTo(Redaction::class, 'redaction_id');
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class, 'exam_attempt_id');
    }
}
