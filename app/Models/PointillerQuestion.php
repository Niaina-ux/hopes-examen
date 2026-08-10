<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PointillerQuestion extends Model
{
    use HasFactory;

    protected $table = 'pointiller_questions';

    protected $fillable = [
        'pointiller_id', 'enonce', 'image', 'video', 'points', 'ordre',
    ];

    public function pointiller()
    {
        return $this->belongsTo(Pointiller::class);
    }

    

    // "trou" iray (position + reponse_correcte) ho an'ity question ity
    public function reponses()
    {
        return $this->hasMany(PointillerReponse::class)->orderBy('position');
    }

    public function getEnonceAvecTrouAttribute()
    {
        $reponses = $this->reponses; // hasMany, efa voarindra araka ny position

        if ($reponses->isEmpty()) {
            return $this->enonce;
        }

        return preg_replace_callback('/\[(\d+)\]/', function ($matches) use ($reponses) {
            $position = (int) $matches[1];

            // Mitady ny reponse mifanaraka amin'ity position ity
            $reponse = $reponses->firstWhere('position', $position);

            if (!$reponse) {
                return $matches[0]; // avelao toy izay ihany raha tsy misy reponse mifanaraka
            }

            $selectName = "reponses[{$reponse->id}]";

            $options = '<option value="">-- Choisir --</option>';
            foreach ($reponse->choices as $choice) {
                $options .= '<option value="' . e($choice->texte) . '">' . e($choice->texte) . '</option>';
            }

            return '<select name="' . $selectName . '" class="border rounded">' . $options . '</select>';
        }, $this->enonce);
    }
}
