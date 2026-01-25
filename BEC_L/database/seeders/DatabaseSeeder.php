<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles (Si no los tenías)
        DB::table('roles')->insertOrIgnore([
            ['id' => 1, 'nombre' => 'Admin', 'descripcion' => 'Administrador total'],
            ['id' => 2, 'nombre' => 'Usuario', 'descripcion' => 'Donante/Voluntario'],
        ]);

        // 2. Géneros
        DB::table('generos')->insertOrIgnore([
            ['id' => 1, 'nombre' => 'Masculino'],
            ['id' => 2, 'nombre' => 'Femenino'],
            ['id' => 3, 'nombre' => 'No binario'],
            ['id' => 4, 'nombre' => 'Prefiero no decir'],
        ]);

        // 3. Países (Ejemplo básico)
        DB::table('paises')->insertOrIgnore([
            ['id' => 1, 'nombre' => 'México'],
            ['id' => 2, 'nombre' => 'Estados Unidos'],
            ['id' => 3, 'nombre' => 'Canadá'],
            ['id' => 4, 'nombre' => 'Colombia'],
            ['id' => 5, 'nombre' => 'Otro'],
        ]);

        // 4. Nacionalidades
        DB::table('nacionalidades')->insertOrIgnore([
            ['id' => 1, 'nombre' => 'Mexicana'],
            ['id' => 2, 'nombre' => 'Estadounidense'],
            ['id' => 3, 'nombre' => 'Canadiense'],
            ['id' => 4, 'nombre' => 'Colombiana'],
            ['id' => 5, 'nombre' => 'Otra'],
        ]);
    }
}
