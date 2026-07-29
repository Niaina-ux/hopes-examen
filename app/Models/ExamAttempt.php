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

    /**
     * Manisa indray ny score manontolo an'ity attempt ity,
     * avy amin'ny totalin'ny points_obtenus rehetra (qcm + pointiller + hafa).
     * Tsy "increment" fa "recalculate from source of truth" mba tsy hisy double counting.
     */
    public function recalculerScore(): float
    {
        $scoreQcm = QcmReponse::where('exam_attempt_id', $this->id)
            ->groupBy('qcm_question_id')
            ->selectRaw('qcm_question_id, MAX(points_obtenus) as points')
            ->get()
            ->sum('points');

        $scorePointiller = PointillerEtudiantReponse::where('exam_attempt_id', $this->id)
            ->sum('points_obtenus');

        $scoreGlisserDeposer = GlisserDeposerReponse::where('exam_attempt_id', $this->id)
            ->sum('points_obtenus');

        $scoreMotsCroiser = MotsCroisesReponse::where('exam_attempt_id', $this->id)
            ->sum('points_obtenus');
        
        $scoreRelier = RelierReponse::where('exam_attempt_id', $this->id)
            ->sum('points_obtenus');
            
        $scoreTotal = $scoreQcm 
                    + $scorePointiller 
                    + $scorePointiller 
                    + $scoreGlisserDeposer
                    + $scoreMotsCroiser
                    + $scoreRelier;

        $this->update(['score' => $scoreTotal]);

        return $scoreTotal;
    }

    public function fichierReponses()
    {
        return $this->hasMany(FichierReponse::class);
    }
}