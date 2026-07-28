<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointillerEtudiantReponse extends Model
{
    protected $table = 'pointiller_etudiant_reponses';

    protected $fillable = [
        'pointiller_reponse_id','exam_attempt_id', 'student_id', 'reponse_donnee',
        'est_correcte', 'points_obtenus',
    ];

    public function reponse()
    {
        return $this->belongsTo(PointillerReponse::class, 'pointiller_reponse_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
