<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fichier extends Model
{
    use HasFactory;

    protected $table = 'fichier';

    protected $fillable = [
        'examen_id',
        'categorie_id',
        'titre',
        'description',
        'duree_minutes',
        'note_totale',
        'ordre',
    ];

    public function examen()
    {
        return $this->belongsTo(Examen::class);
    }

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function fichierQuestions()
    {
        return $this->hasMany(fichierQuestion::class)
            ->orderBy('ordre');
    }
}
