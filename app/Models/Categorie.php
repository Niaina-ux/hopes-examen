<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categorie extends Model
{
    protected $fillable = ['nom','slug'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
    
    public function profs()
    {
        return $this->hasMany(Prof::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function examens(): HasMany
    {
        return $this->hasMany(Examen::class, 'categorie_id');
    }

    public function typesExerciceAutorises(): BelongsToMany
    {
        return $this->belongsToMany(TypeExercice::class, 'categorie_type_exercice');
    }
}
