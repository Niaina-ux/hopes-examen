<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamenTypeExercice extends Model
{
    use HasFactory;

    protected $table = 'examen_type_exercice';

    protected $fillable = [
        'examen_id',
        'type_exercice_id',
        'ordre',
    ];

    protected $casts = [
        'ordre' => 'integer',
    ];

    /**
     * Ny examen misy an'ity enregistrement ity
     */
    public function examen(): BelongsTo
    {
        return $this->belongsTo(Examen::class, 'examen_id');
    }

    /**
     * Ny type d'exercice mifandray amin'ity examen ity
     */
    public function typeExercice(): BelongsTo
    {
        return $this->belongsTo(TypeExercice::class, 'type_exercice_id');
    }
}
