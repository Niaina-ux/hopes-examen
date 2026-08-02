<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GlisserDeposerItem extends Model
{
    use HasFactory;

    protected $table = 'glisser_deposer_items';

    protected $fillable = [
        'glisser_deposer_question_id',
        'glisser_deposer_zone_id',
        'texte',
        'ordre',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(GlisserDeposerQuestion::class, 'glisser_deposer_question_id');
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(GlisserDeposerZone::class, 'glisser_deposer_zone_id');
    }

    public function reponses()
    {
        return $this->hasMany(GlisserDeposerReponse::class, 'glisser_deposer_item_id');
    }

}
