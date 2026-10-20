<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            // Otros seeders que tengas...
            DefaultUserSeeder::class, // Llamada a tu seeder
        ]);
    }
}
