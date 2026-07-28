<?php

namespace Database\Seeders;

use App\Models\Categorie;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['nom' => 'Français', 'slug' => 'francais'],
            ['nom' => 'Anglais', 'slug' => 'anglais'],
            ['nom' => 'Développement Web', 'slug' => 'web'],
            ['nom' => 'Python', 'slug' => 'python'],
            ['nom' => 'Design', 'slug' => 'design'],
            ['nom' => 'Bureautique', 'slug' => 'bureautique'],
        ];

        foreach ($categories as $categorie) {
            Categorie::updateOrCreate(
                ['slug' => $categorie['slug']],
                ['nom' => $categorie['nom']]
            );
        }
    }
}
