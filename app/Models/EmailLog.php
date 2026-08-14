<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailLog extends Model
{
    protected $fillable = ['user_id', 'type', 'examen_id', 'sujet'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function examen(): BelongsTo
    {
        return $this->belongsTo(Examen::class);
    }
}
