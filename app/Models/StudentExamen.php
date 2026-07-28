<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentExamen extends Model
{
    use HasFactory;

    protected $table = 'student_examen';

    protected $fillable = [
        'examen_id',
        'user_id',
        'termine',
        'date_examen',

    ];

    protected $casts = [
        'termine'    => 'boolean',
        'date_examen' => 'datetime',
    ];

    /**
     * Ny examen misy an'ity enregistrement ity
     */
    public function examen(): BelongsTo
    {
        return $this->belongsTo(Examen::class, 'examen_id');
    }

    /**
     * Ny mpampiasa (student) mandray anjara amin'ny examen
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
