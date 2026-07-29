<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class GlisserDeposerZone extends Model
{
    use HasFactory;

    protected $table = 'glisser_deposer_zones';

    protected $fillable = [
        'glisser_deposer_question_id',
        'nom_zone',
        'position_x',
        'position_y',
        'ordre',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(GlisserDeposerQuestion::class, 'glisser_deposer_question_id');
    }

    public function item(): HasOne
    {
        return $this->hasOne(GlisserDeposerItem::class, 'glisser_deposer_zone_id');
    }
}
