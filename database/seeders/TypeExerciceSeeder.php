<?php

namespace Database\Seeders;

use App\Models\TypeExercice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeExerciceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $exercices = [
            ['nom' => 'Question à choix muliple', 'slug' => 'qcm'],
            ['nom' => 'Relier par flèche', 'slug' => 'relier'],
            ['nom' => 'Compléter le pointiller', 'slug' => 'pointiller'],
            ['nom' => 'Code', 'slug' => 'code'],
        ];

        foreach ($exercices as $exercice) {
            TypeExercice::updateOrCreate(
                ['slug' => $exercice['slug']],
                ['nom' => $exercice['nom']]
            );
        }
    }
}
