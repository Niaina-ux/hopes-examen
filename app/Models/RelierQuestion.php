<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RelierQuestion extends Model
{
    use HasFactory;

    protected $table = 'relier_questions';

    protected $fillable = [
        'relier_id',
        'enonce',
        'points',
        'ordre',
    ];

    public function relier(): BelongsTo
    {
        return $this->belongsTo(Relier::class, 'relier_id');
    }

    public function paires(): HasMany
    {
        return $this->hasMany(RelierPaire::class, 'relier_question_id')->orderBy('ordre_gauche');
    }
}
