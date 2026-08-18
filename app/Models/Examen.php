<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Examen extends Model
{
    protected $fillable = ['titre', 'description', 'categorie_id', 'duree_minutes','date_examen','status'];

    protected $casts = [
        'date_examen' => 'date',
    ];

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class, 'categorie_id');
    }

    public function typesExercice(): BelongsToMany
    {
        return $this->belongsToMany(TypeExercice::class, 'examen_type_exercice', 'examen_id', 'type_exercice_id')
            ->withPivot('ordre')
            ->withTimestamps()
            ->orderByPivot('ordre');
    }

    public function qcm()
    {
        return $this->hasMany(Qcm::class);
    }

    public function pointiller()
    {
        return $this->hasMany(Pointiller::class);
    }

    public function relier()
    {
        return $this->hasMany(Relier::class);
    }

    public function code()
    {
        return $this->hasMany(Code::class);
    }

    public function fichier()
    {
        return $this->hasMany(Fichier::class);
    }

    public function texts()
    {
        return $this->hasMany(Text::class);
    }

    public function glisserDeposers()
    {
        return $this->hasMany(GlisserDeposer::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'student_examen')
            ->withPivot('termine', 'date_debut', 'date_fin')
            ->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'examen_etudiant', 'examen_id', 'user_id')
                    ->withPivot('termine', 'termine_le', 'date_debut')
                    ->withTimestamps();
    }

    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

}
