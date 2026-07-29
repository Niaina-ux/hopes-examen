<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlisserDeposerReponse extends Model
{
    protected $table = 'glisser_deposer_reponses';

    protected $fillable = [
        'glisser_deposer_item_id', 'glisser_deposer_zone_id',
        'exam_attempt_id', 'student_id', 'est_correcte', 'points_obtenus',
    ];

    public function item() { return $this->belongsTo(GlisserDeposerItem::class, 'glisser_deposer_item_id'); }
    public function zoneChoisie() { return $this->belongsTo(GlisserDeposerZone::class, 'glisser_deposer_zone_id'); }

}
