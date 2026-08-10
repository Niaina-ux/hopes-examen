<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImageExercice extends Model
{
    use HasFactory;

    protected $table = 'image_exercice';

    protected $fillable = [
        'examen_id', 'categorie_id', 'titre', 'description',
        'duree_minutes', 'note_totale', 'ordre',
    ];

    public function examen(): BelongsTo
    {
        return $this->belongsTo(Examen::class, 'examen_id');
    }

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class, 'categorie_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(ImageExerciceQuestion::class, 'image_exercice_id')->orderBy('ordre');
    }
}
