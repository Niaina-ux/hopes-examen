<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Code extends Model
{
    use HasFactory;

    protected $table = 'code';

    protected $fillable = [
        'examen_id',
        'categorie_id',
        'titre',
        'description',
        'note_totale',
        'ordre',
    ];

    public function examen(): BelongsTo
    {
        return $this->belongsTo(Examen::class, 'examen_id');
    }

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class, 'categorie_id');
    }

    public function codeQuestions(): HasMany
    {
        return $this->hasMany(CodeQuestion::class, 'code_id')->orderBy('ordre');
    }
}
