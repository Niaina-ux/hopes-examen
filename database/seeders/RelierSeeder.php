<?php

namespace Database\Seeders;

use App\Models\Examen;
use App\Models\Relier;
use App\Models\RelierPaire;
use App\Models\RelierQuestion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RelierSeeder extends Seeder
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

        $modeles = [
            [
                'enonce' => 'Reliez chaque pays à sa capitale.',
                'paires' => [
                    ['gauche' => 'France',    'droite' => 'Paris'],
                    ['gauche' => 'Italie',    'droite' => 'Rome'],
                    ['gauche' => 'Espagne',   'droite' => 'Madrid'],
                    ['gauche' => 'Allemagne', 'droite' => 'Berlin'],
                ],
            ],
            [
                'enonce' => 'Reliez chaque mot anglais à sa traduction française.',
                'paires' => [
                    ['gauche' => 'Book',    'droite' => 'Livre'],
                    ['gauche' => 'Table',   'droite' => 'Table'],
                    ['gauche' => 'Water',   'droite' => 'Eau'],
                    ['gauche' => 'House',   'droite' => 'Maison'],
                    ['gauche' => 'Friend',  'droite' => 'Ami'],
                ],
            ],
        ];

        foreach ($examens as $examen) {

            $relier = Relier::create([
                'examen_id'     => $examen->id,
                'categorie_id'  => $examen->categorie_id,
                'titre'         => 'Relier - Associations',
                'description'   => 'Exercice de mise en relation par flèche.',
                'duree_minutes' => 10,
                'note_totale'   => 20,
                'ordre'         => 1,
            ]);

            foreach ($modeles as $indexQuestion => $modele) {

                $question = RelierQuestion::create([
                    'relier_id' => $relier->id,
                    'enonce'    => $modele['enonce'],
                    'points'    => 5,
                    'ordre'     => $indexQuestion + 1,
                ]);

                $paires = $modele['paires'];

                // ✅ Mamorona ny ordre_gauche (mitovy amin'ny filaharana voajanahary)
                $ordresGauche = range(1, count($paires));

                // ✅ Mifangaro ny ordre_droite, mba tsy hitovy amin'ny ordre_gauche
                $ordresDroite = $ordresGauche;
                shuffle($ordresDroite);

                foreach ($paires as $indexPaire => $paire) {
                    RelierPaire::create([
                        'relier_question_id' => $question->id,
                        'element_gauche'     => $paire['gauche'],
                        'image_gauche'       => null,
                        'element_droite'     => $paire['droite'],
                        'image_droite'       => null,
                        'ordre_gauche'       => $ordresGauche[$indexPaire],
                        'ordre_droite'       => $ordresDroite[$indexPaire],
                    ]);
                }
            }
        }
    }
}
