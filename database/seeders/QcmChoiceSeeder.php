<?php

namespace Database\Seeders;

use App\Models\QcmChoice;
use App\Models\QcmQuestion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QcmChoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions = QcmQuestion::all();

        if ($questions->isEmpty()) {
            $this->command->warn('Tsy misy qcm_questions ao amin\'ny table, mila mandefa QcmQuestionSeeder aloha.');
            return;
        }

        foreach ($questions as $question) {
            $choices = $this->getChoicesFor($question);

            foreach ($choices as $index => $choice) {
                QcmChoice::create([
                    'qcm_question_id' => $question->id,
                    'texte'           => $choice['texte'],
                    'est_correcte'    => $choice['est_correcte'],
                    'ordre'           => $index + 1,
                ]);
            }
        }
    }

    
    private function getChoicesFor(QcmQuestion $question): array
    {
        // Raha true_false
        if ($question->reponse_type === 'true_false') {
            // Ohatra: "La Terre est plate." -> Faux no marina
            $estVrai = str_contains($question->enonce, 'lève à l\'est');

            return [
                ['texte' => 'Vrai', 'est_correcte' => $estVrai],
                ['texte' => 'Faux', 'est_correcte' => !$estVrai],
            ];
        }

        // Raha single (valiny tokana marina)
        if ($question->reponse_type === 'single') {
            return [
                ['texte' => 'Antananarivo', 'est_correcte' => true],
                ['texte' => 'Toamasina', 'est_correcte' => false],
                ['texte' => 'Fianarantsoa', 'est_correcte' => false],
                ['texte' => 'Mahajanga', 'est_correcte' => false],
            ];
        }

        // Raha multiple (valiny maromaro marina)
        if ($question->reponse_type === 'multiple') {
            return [
                ['texte' => 'PHP', 'est_correcte' => true],
                ['texte' => 'JavaScript', 'est_correcte' => true],
                ['texte' => 'HTML', 'est_correcte' => false],
                ['texte' => 'Python', 'est_correcte' => true],
                ['texte' => 'CSS', 'est_correcte' => false],
            ];
        }

        // default fallback
        return [
            ['texte' => 'Choix 1', 'est_correcte' => true],
            ['texte' => 'Choix 2', 'est_correcte' => false],
        ];
    }
}
