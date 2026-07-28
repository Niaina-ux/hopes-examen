<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TextQuestion extends Model
{
    use HasFactory;

    protected $table = 'text_questions';

    protected $fillable = [
        'text_id',
        'enonce',
        'points',
        'ordre',
    ];

    public function text(): BelongsTo
    {
        return $this->belongsTo(Text::class, 'text_id');
    }

    public function reponses(): HasMany
    {
        return $this->hasMany(TextReponse::class, 'text_question_id');
    }
}
