<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RelierPaire extends Model
{
    use HasFactory;

    protected $table = 'relier_paires';

    protected $fillable = [
        'relier_question_id',
        'element_gauche',
        'image_gauche',
        'element_droite',
        'image_droite',
        'ordre_gauche',
        'ordre_droite',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(RelierQuestion::class, 'relier_question_id');
    }
}
