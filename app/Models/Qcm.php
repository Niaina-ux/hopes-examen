<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Qcm extends Model
{
    protected $table = 'qcm';

    protected $fillable = ['examen_id','categorie_id','titre', 'description', 'duree_minutes', 'note_totale'];

    public function examen()
    {
        return $this->belongsTo(Examen::class);
    }

    public function qcm()
    {
        return $this->hasMany(Qcm::class)
                    ->orderBy('ordre');
    }

    public function qcmQuestions(): HasMany
    {
        return $this->hasMany(QcmQuestion::class, 'qcm_id');
    }
}
