<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Redaction extends Model
{
    use HasFactory;

    protected $table = 'redactions';

    protected $fillable = [
        'examen_id',
        'categorie_id',
        'titre',
        'sujet',
        'instruction',
        'nombre_mots_min',
        'nombre_mots_max',
        'duree_minutes',
        'note_totale',
        'ordre',
    ];

    public function examen(): BelongsTo
    {
        return $this->belongsTo(Examen::class, 'examen_id');
    }

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class, 'categorie_id');
    }

    public function reponses(): HasMany
    {
        return $this->hasMany(RedactionReponse::class, 'redaction_id');
    }
}
