<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImageExerciceQuestion extends Model
{
    use HasFactory;

    protected $table = 'image_exercice_question';

    protected $fillable = [
        'image_exercice_id', 'instruction', 'image', 'points', 'ordre',
    ];

    public function imageExercice(): BelongsTo
    {
        return $this->belongsTo(ImageExercice::class, 'image_exercice_id');
    }

    public function reponses(): HasMany
    {
        return $this->hasMany(ImageExerciceReponse::class, 'image_exercice_question_id');
    }
}
