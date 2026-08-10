<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PointillerReponse extends Model
{
    protected $table = 'pointiller_reponses';

    protected $fillable = ['pointiller_question_id', 'position', 'reponse_correcte'];

    public function question()
    {
        return $this->belongsTo(PointillerQuestion::class, 'pointiller_question_id');
    }

    // banque de mots ho an'ity "trou" ity
    public function choices()
    {
        return $this->hasMany(PointillerChoice::class);
    }

    public function etudiantReponses(): HasMany
    {
        return $this->hasMany(PointillerEtudiantReponse::class, 'pointiller_reponse_id');
    }
}
