<?php

namespace Database\Seeders;

use App\Models\Categorie;
use App\Models\Examen;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExamenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $examens = [
            [
                'categorie_id'   => 1,
                'titre'          => 'Examen de Mathématiques - Niveau 1',
                'description'    => 'Test portant sur les bases de l\'algèbre et de la géométrie.',
                'duree_minutes'  => 60,
                'status'         => 'brouillon',
            ],
            [
                'categorie_id'   => 2,
                'titre'          => 'Examen de Français - Compréhension écrite',
                'description'    => 'Évaluation de la compréhension de texte et de la grammaire.',
                'duree_minutes'  => 45,
                'status'         => 'brouillon',
            ],
            [
                'categorie_id'   => 3,
                'titre'          => 'Examen de Culture Générale',
                'description'    => 'Questions variées sur l\'actualité, l\'histoire et les sciences.',
                'duree_minutes'  => 30,
                'status'         => 'brouillon',
            ],
            [
                'categorie_id'   => 4,
                'titre'          => 'Examen de Sciences Physiques',
                'description'    => 'Test sur la mécanique, l\'électricité et la chimie de base.',
                'duree_minutes'  => 50,
                'status'         => 'brouillon',
            ],
            [
                'categorie_id'   => 2,
                'titre'          => 'Examen d\'Histoire-Géographie',
                'description'    => 'Évaluation sur les grands événements historiques et la géographie mondiale.',
                'duree_minutes'  => 40,
                'status'         => 'archive',
            ],
        ];

        foreach ($examens as $examen) {
            Examen::create($examen);
        }
    }
}
