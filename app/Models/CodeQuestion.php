<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CodeQuestion extends Model
{
    use HasFactory;

    protected $table = 'code_questions';

    protected $fillable = [
        'code_id',
        'instruction',
        'langage',
        'code_starter',
        'points',
        'ordre',
    ];

    public function code(): BelongsTo
    {
        return $this->belongsTo(Code::class, 'code_id');
    }

    public function reponses(): HasMany
    {
        return $this->hasMany(CodeReponse::class, 'code_question_id');
    }
}
