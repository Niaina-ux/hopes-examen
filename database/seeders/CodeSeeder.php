<?php

namespace Database\Seeders;

use App\Models\Code;
use App\Models\CodeQuestion;
use App\Models\Examen;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $examen = Examen::find(3);

        if (!$examen) {
            $this->command->warn('Tsy misy examen misy id=3.');
            return;
        }

        $code = Code::create([
            'examen_id'    => $examen->id,
            'categorie_id' => $examen->categorie_id,
            'titre'        => 'Exercice de Code',
            'description'  => 'Résolvez les problèmes de programmation suivants.',
            'note_totale'  => 20,
            'ordre'        => 1,
        ]);

        $questions = [
            [
                'instruction'  => 'Écrivez une fonction PHP qui retourne la somme de deux nombres.',
                'langage'      => 'php',
                'code_starter' => "function addition(\$a, \$b) {\n    // Votre code ici\n}",
                'points'       => 5,
                'ordre'        => 1,
            ],
            [
                'instruction'  => 'Écrivez une fonction JavaScript qui vérifie si un nombre est pair.',
                'langage'      => 'javascript',
                'code_starter' => "function estPair(nombre) {\n    // Votre code ici\n}",
                'points'       => 5,
                'ordre'        => 2,
            ],
            [
                'instruction'  => 'Écrivez une fonction Python qui inverse une chaîne de caractères.',
                'langage'      => 'python',
                'code_starter' => "def inverser_chaine(texte):\n    # Votre code ici\n    pass",
                'points'       => 5,
                'ordre'        => 3,
            ],
            [
                'instruction'  => 'Écrivez une requête SQL qui sélectionne tous les étudiants de la table students.',
                'langage'      => 'sql',
                'code_starter' => "-- Votre requête ici\nSELECT ",
                'points'       => 5,
                'ordre'        => 4,
            ],
        ];

        foreach ($questions as $question) {
            CodeQuestion::create([
                'code_id'  => $code->id,
                'instruction'  => $question['instruction'],
                'langage'      => $question['langage'],
                'code_starter' => $question['code_starter'],
                'points'       => $question['points'],
                'ordre'        => $question['ordre'],
            ]);
        }
    }
}
