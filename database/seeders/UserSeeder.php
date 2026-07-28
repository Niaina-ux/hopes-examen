<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@exam.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Prof Test',
            'email' => 'prof@exam.com',
            'password' => Hash::make('password'),
            'role' => 'prof',
        ]);

        User::create([
            'name' => 'Student Test',
            'email' => 'student@exam.com',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);
    }
}
