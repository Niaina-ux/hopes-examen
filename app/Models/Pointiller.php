<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pointiller extends Model
{
    use HasFactory;

    protected $table = 'pointiller';

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

    public function pointillerQuestions()
    {
        return $this->hasMany(PointillerQuestion::class);
    }
}
