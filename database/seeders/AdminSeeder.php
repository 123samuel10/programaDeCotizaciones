<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
 public function run(): void
    {
        // Crear usuario administrador
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'], // si ya existe, no lo duplica
            [
                'name' => 'Administrador',
                'password' => Hash::make('123admin@'),
                'rol' => 'administrador',
            ]
        );
    }
}
