<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class DefaultUserSeeder extends Seeder
{
    public function run()
    {
        // Verificar si existe un usuario con este email
        $existing = User::where('email', 'admin@example.com')->first();
        if (!$existing) {
            // Si no existe, lo creamos
            User::create([
                'name' => 'Admin Default',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]);
        }
    }
}

