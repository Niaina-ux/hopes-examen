<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MotsCroisesReponse extends Model
{
    use HasFactory;

    protected $table = 'mots_croises_reponses';

    protected $fillable = [
        'mots_croises_mot_id',
        'exam_attempt_id',
        'student_id',
        'reponse_donnee',
        'est_correcte',
        'points_obtenus',
    ];

    protected $casts = [
        'est_correcte'   => 'boolean',
        'points_obtenus' => 'decimal:2',
    ];

    public function mot(): BelongsTo
    {
        return $this->belongsTo(MotsCroisesMot::class, 'mots_croises_mot_id');
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class, 'exam_attempt_id');
    }
}
