<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QcmChoice extends Model
{
    protected $table = 'qcm_choices';

    protected $fillable = ['qcm_question_id', 'texte', 'est_correcte', 'ordre'];

    protected $casts = [
        'est_correcte' => 'boolean',
    ];

    public function qcmQuestion()
    {
        return $this->belongsTo(QcmQuestion::class, 'qcm_web_question_id');
    }
}
