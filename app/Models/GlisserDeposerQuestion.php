<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GlisserDeposerQuestion extends Model
{
    use HasFactory;

    protected $table = 'glisser_deposer_questions';

    protected $fillable = [
        'glisser_deposer_id',
        'enonce',
        'image',
        'image_largeur',
        'image_hauteur',
        'points',
        'ordre',
    ];

    public function glisserDeposer(): BelongsTo
    {
        return $this->belongsTo(GlisserDeposer::class, 'glisser_deposer_id');
    }

    public function zones(): HasMany
    {
        return $this->hasMany(GlisserDeposerZone::class, 'glisser_deposer_question_id')->orderBy('ordre');
    }

    public function items(): HasMany
    {
        return $this->hasMany(GlisserDeposerItem::class, 'glisser_deposer_question_id')->orderBy('ordre');
    }
}
