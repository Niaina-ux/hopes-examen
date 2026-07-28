<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MotsCroisesWeb extends Model
{
    protected $table = 'mots_croises_webs';

    protected $fillable = ['examen_id', 'titre', 'description', 'note_totale'];

    public function examen()
    {
        return $this->belongsTo(Examen::class);
    }
}
