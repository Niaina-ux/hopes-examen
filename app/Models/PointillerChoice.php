<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointillerChoice extends Model
{
    protected $table = 'pointiller_choices';

    protected $fillable = ['pointiller_reponse_id', 'texte'];

    public function reponse()
    {
        return $this->belongsTo(PointillerReponse::class, 'pointiller_reponse_id');
    }
}
