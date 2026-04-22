<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Orquestador de seeders: conviene un seeder por módulo / dominio.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UsuarioSeeder::class,
        ]);
    }
}
