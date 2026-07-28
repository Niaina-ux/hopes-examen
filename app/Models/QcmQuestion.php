<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QcmQuestion extends Model
{
    protected $table = 'qcm_questions';

    protected $fillable = [
        'qcm_id', 'enonce', 'image', 'video',
        'reponse_type', 'points','duree_seconde','ordre',
    ];

    public function qcm()
    {
        return $this->belongsTo(Qcm::class);
    }

    public function qcmChoices(): HasMany  // na "qcmChoices" raha tsy "Web" no ampiasainao
    {
        return $this->hasMany(QcmChoice::class, 'qcm_question_id');
    }

    public function qcmReponsesEtudiants()
    {
        return $this->hasMany(QcmReponse::class, 'qcm_question_id');
    }
}
