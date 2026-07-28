<?php

namespace Database\Seeders;

use App\Models\Qcm;
use App\Models\QcmQuestion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QcmQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $qcms = Qcm::all();

        if ($qcms->isEmpty()) {
            $this->command->warn('Tsy misy qcm ao amin\'ny table, mila mandefa QcmSeeder aloha.');
            return;
        }

        $modeles = [
            [
                'enonce'        => 'La Terre est plate.',
                'reponse_type'  => 'true_false',
                'points'        => 1,
                'duree_seconde' => 15,
            ],
            [
                'enonce'        => 'Quelle est la capitale de Madagascar ?',
                'reponse_type'  => 'single',
                'points'        => 2,
                'duree_seconde' => 20,
            ],
            [
                'enonce'        => 'Parmi les propositions suivantes, lesquelles sont des langages de programmation ?',
                'reponse_type'  => 'multiple',
                'points'        => 3,
                'duree_seconde' => 30,
            ],
            [
                'enonce'        => 'Le soleil se lève à l\'est.',
                'reponse_type'  => 'true_false',
                'points'        => 1,
                'duree_seconde' => 10,
            ],
        ];

        foreach ($qcms as $qcm) {
            foreach ($modeles as $index => $modele) {
                    QcmQuestion::create([
                    'qcm_id'        => $qcm->id,
                    'enonce'        => $modele['enonce'],
                    'image'         => null,
                    'video'         => null,
                    'reponse_type'  => $modele['reponse_type'],
                    'points'        => $modele['points'],
                    'duree_seconde' => $modele['duree_seconde'],
                    'ordre'         => $index + 1,
                ]);
            }
        }
    }
}
