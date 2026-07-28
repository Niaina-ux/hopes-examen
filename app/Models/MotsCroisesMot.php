<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MotsCroisesMot extends Model
{
    use HasFactory;

    protected $table = 'mots_croises_mots';

    protected $fillable = [
        'mots_croises_id',
        'indice',
        'reponse',
        'direction',
        'position_x',
        'position_y',
        'numero',
        'points',
        'positions_lettres_visibles',
    ];
    
    protected $casts = [
        'positions_lettres_visibles' => 'array', // ✅ AMPIO ITY — mamadika array <-> JSON automatika
    ];

    public function motsCroises(): BelongsTo
    {
        return $this->belongsTo(MotsCroises::class, 'mots_croises_id');
    }

    public function reponses(): HasMany
    {
        return $this->hasMany(MotsCroisesReponse::class, 'mots_croises_mot_id');
    }
}
