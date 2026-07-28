<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FichierReponse extends Model
{
    use HasFactory;

    protected $table = 'fichier_reponses';

    protected $fillable = [
        'fichier_question_id',
        'exam_attempt_id',
        'student_id',
        'fichier_etudiant',
        'points_obtenus',
        'commentaire_prof',
        'est_corrige',
    ];

    /**
     * Question mifandray amin'ity valiny ity
     */
    public function fichierQuestion()
    {
        return $this->belongsTo(FichierQuestion::class);
    }

    /**
     * Tentative examen
     */
    public function examAttempt()
    {
        return $this->belongsTo(ExamAttempt::class);
    }

    /**
     * Mpianatra (User)
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
