<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;

/**
 * Seeders: poblan tablas con datos iniciales o de demostración.
 */
class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        Usuario::factory()->count(5)->create();
    }
}
