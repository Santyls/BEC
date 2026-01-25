<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50)->unique();
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });

        // 2. Géneros
        Schema::create('generos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50)->unique();
            $table->timestamps();
        });

        // 3. Países
        Schema::create('paises', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
            $table->timestamps();
        });

        // 4. Nacionalidades
        Schema::create('nacionalidades', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
            $table->timestamps();
        });
        
        // Insertar datos semilla básicos (Seeders ligeros)
        // Esto es útil para que el sistema no arranque vacío
        DB::table('roles')->insert([
            ['nombre' => 'Admin', 'descripcion' => 'Administrador total'],
            ['nombre' => 'Usuario', 'descripcion' => 'Usuario general/Donante'],
            ['nombre' => 'Voluntario', 'descripcion' => 'Usuario verificado para voluntariado'],
        ]);

        DB::table('generos')->insert([
            ['nombre' => 'Masculino'],
            ['nombre' => 'Femenino'],
            ['nombre' => 'Otro'],
            ['nombre' => 'Prefiero no decir'],
        ]);
    }

    public function down(): void
    {
        // El orden de borrado importa (aunque aquí no tienen FKs entre ellas)
        Schema::dropIfExists('nacionalidades');
        Schema::dropIfExists('paises');
        Schema::dropIfExists('generos');
        Schema::dropIfExists('roles');
    }
};