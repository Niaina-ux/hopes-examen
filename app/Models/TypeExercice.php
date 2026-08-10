<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class TypeExercice extends Model
{
    protected $table = 'types_exercice';
    
    protected $fillable = ['nom', 'slug', 'icone'];

    public function examens()
    {
        return $this->belongsToMany(Examen::class, 'examen_type_exercice');
    }

    public function commentaires(): MorphMany
    {
        return $this->morphMany(Commentaire::class, 'commentable');
    }
}
