<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cria um usuário administrador padrão
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@tiaocarreiro.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        // Cria um usuário de teste
        User::create([
            'name' => 'Usuário Teste',
            'email' => 'teste@tiaocarreiro.com',
            'password' => Hash::make('teste123'),
            'email_verified_at' => now(),
        ]);
    }
}
