<?php

namespace Database\Seeders;

use App\Models\Examen;
use App\Models\Pointiller;
use App\Models\PointillerChoice;
use App\Models\PointillerQuestion;
use App\Models\PointillerReponse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PointillerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $examen = Examen::first(); // ovay araka ny Examen tena tianao

        if (!$examen) {
            $this->command->warn('Aucun examen trouvé. Créez un examen avant de lancer ce seeder.');
            return;
        }

        $pointiller = Pointiller::create([
            'examen_id' => $examen->id,
            'categorie_id' => $examen->categorie_id,
            'titre' => 'Compléter le pointillé — Web',
            'description' => 'Testez vos connaissances de base sur le développement web.',
            'duree_minutes' => 10,
            'note_totale' => 10,
            'ordre' => 0,
        ]);

        $question = PointillerQuestion::create([
            'pointiller_id' => $pointiller->id,
            'enonce' => 'Le [1] web est l\'ensemble de code parfait.',
            'points' => 5,
            'ordre' => 0,
        ]);

        $reponse = PointillerReponse::create([
            'pointiller_question_id' => $question->id,
            'position' => 1,
            'reponse_correcte' => 'developpement',
        ]);

        $choices = ['developpement', 'design', 'marketing'];

        foreach ($choices as $texte) {
            PointillerChoice::create([
                'pointiller_reponse_id' => $reponse->id,
                'texte' => $texte,
            ]);
        }
    }
}
