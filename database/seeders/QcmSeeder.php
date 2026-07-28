<?php

namespace Database\Seeders;

use App\Models\Examen;
use App\Models\Qcm;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QcmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $examens = Examen::all();

        if ($examens->isEmpty()) {
            $this->command->warn('Tsy misy examen ao amin\'ny table, mila mandefa ExamenSeeder aloha.');
            return;
        }

        foreach ($examens as $examen) {
            for ($i = 1; $i <= 2; $i++) {
                Qcm::create([
                    'examen_id'      => $examen->id,
                    'categorie_id'   => $examen->categorie_id, 
                    'titre'          => 'QCM ' . $i . ' - ' . $examen->titre,
                    'description'    => 'Questionnaire à choix multiples numéro ' . $i . ' lié à ' . $examen->titre,
                    'duree_minutes'  => 10,
                    'note_totale'    => 10,
                    'ordre'          => $i,
                ]);
            }
        }
    }
}
