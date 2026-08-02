<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QcmReponse extends Model
{
    protected $table = 'qcm_reponses';

    protected $fillable = [
        'qcm_question_id',
        'qcm_choice_id',
        'exam_attempt_id', 
        'student_id',
        'est_correcte',
        'points_obtenus'
    ];

    public function qcmQuestion()
    {
        return $this->belongsTo(QcmQuestion::class, 'qcm_question_id');
    }

    public function qcmchoice()
    {
        return $this->belongsTo(QcmChoice::class, 'qcm_choice_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function question()
    {
        return $this->belongsTo(QcmQuestion::class, 'question_id');
    }
}
