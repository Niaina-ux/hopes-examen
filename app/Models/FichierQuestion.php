<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FichierQuestion extends Model
{
    use HasFactory;

    protected $table = 'fichier_questions';

    protected $fillable = [
        'fichier_id',
        'instruction',
        'fichier_prof',
        'points',
        'ordre',
    ];

    public function fichier()
    {
        return $this->belongsTo(Fichier::class);
    }

    public function reponses(): HasMany
    {
        return $this->hasMany(FichierReponse::class, 'fichier_question_id');
    }
}
