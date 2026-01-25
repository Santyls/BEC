<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('direcciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            
            // Ubicación
            $table->foreignId('pais_id')->constrained('paises'); // Usamos el catálogo existente
            $table->string('estado', 100);
            $table->string('municipio', 100);
            $table->string('colonia', 100);
            $table->string('codigo_postal', 10);
            
            // Detalles de calle
            $table->string('calle', 150);
            $table->string('num_exterior', 20);
            $table->string('num_interior', 20)->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('direcciones');
    }
};