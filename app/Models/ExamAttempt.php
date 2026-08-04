<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamAttempt extends Model
{
    use HasFactory;

    protected $table = 'exam_attempts';

    protected $fillable = [
        'student_id',
        'examen_id',
        'numero_tentative',
        'status',
        'date_debut',
        'date_fin',
        'score',
        'note_total',
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin'   => 'datetime',
        'score'      => 'decimal:2',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function examen(): BelongsTo
    {
        return $this->belongsTo(Examen::class, 'examen_id');
    }

    public function estEnCours(): bool
    {
        return $this->status === 'en_cours';
    }


    public function calculerNoteTotal(): float
    {
        $examenId = $this->examen_id;

        $totalQcm = Qcm::where('examen_id', $examenId)->sum('note_totale');
        $totalPointiller = Pointiller::where('examen_id', $examenId)->sum('note_totale');
        $totalRelier = Relier::where('examen_id', $examenId)->sum('note_totale');
        $totalGlisserDeposer = GlisserDeposer::where('examen_id', $examenId)->sum('note_totale');
        $totalMotsCroises = MotsCroises::where('examen_id', $examenId)->sum('note_totale');
        $totalCode = Code::where('examen_id', $examenId)->sum('note_totale');
        $totalText = Text::where('examen_id', $examenId)->sum('note_totale');
        $totalRedaction = Redaction::where('examen_id', $examenId)->sum('note_totale');
        $totalFichier = Fichier::where('examen_id', $examenId)->sum('note_totale');

        $noteTotal = $totalQcm
            + $totalPointiller
            + $totalRelier
            + $totalGlisserDeposer
            + $totalMotsCroises
            + $totalCode
            + $totalText
            + $totalRedaction
            + $totalFichier;

        // ✅ Tehirizina AO ALOHAN'NY return
        $this->update([
            'note_total' => $noteTotal,
        ]);

        return $noteTotal;
    }

    public function fichierReponses()
    {
        return $this->hasMany(FichierReponse::class);
    }
}