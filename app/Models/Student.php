<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = ['user_id', 'matricule', 'categorie_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function examens()
    {
        return $this->belongsToMany(Examen::class, 'examen_etudiant', 'student_id', 'examen_id')  // examen_etudiant
                    ->withPivot('termine', 'termine_le', 'date_debut')
                    ->withTimestamps();
    }
}
