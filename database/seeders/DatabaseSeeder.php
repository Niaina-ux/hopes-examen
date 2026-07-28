<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            UserSeeder::class,
            CategorieSeeder::class,
            StudentSeeder::class,
            // TypeExerciceSeeder::class,
            // ExamenSeeder::class,
            // QcmSeeder::class,
            // QcmQuestionSeeder::class,
            // QcmChoiceSeeder::class,
            // PointillerSeeder::class,
            // RelierSeeder::class,
            // CodeSeeder::class,
        ]);
    }
}
