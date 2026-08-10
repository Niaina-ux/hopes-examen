<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImageExerciceReponse extends Model
{
    use HasFactory;

    protected $table = 'image_exercice_reponse';

    protected $fillable = [
        'image_exercice_question_id', 'exam_attempt_id', 'student_id',
        'image_soumise', 'points_obtenus', 'commentaire_prof', 'est_corrige',
    ];

    protected $casts = [
        'est_corrige' => 'boolean',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(ImageExerciceQuestion::class, 'image_exercice_question_id');
    }
}
