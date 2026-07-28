<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RelierReponse extends Model
{
    use HasFactory;

    protected $table = 'relier_reponses';

    protected $fillable = [
        'relier_paire_id',
        'exam_attempt_id',
        'student_id',
        'paire_choisie_id',
        'est_correcte',
        'points_obtenus',
    ];

    protected $casts = [
        'est_correcte'   => 'boolean',
        'points_obtenus' => 'decimal:2',
    ];

    public function paire(): BelongsTo
    {
        return $this->belongsTo(RelierPaire::class, 'relier_paire_id');
    }

    public function paireChoisie(): BelongsTo
    {
        return $this->belongsTo(RelierPaire::class, 'paire_choisie_id');
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class, 'exam_attempt_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
