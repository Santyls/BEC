<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            
            // --- DATOS OBLIGATORIOS (Registro Inicial) ---
            $table->string('alias', 50)->unique();
            $table->string('email', 120)->unique();
            $table->string('password');
            $table->foreignId('rol_id')->default(2)->constrained('roles'); 
            
            // --- DATOS DE PERFIL 
            $table->string('nombre', 100)->nullable();
            $table->integer('edad')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('foto_perfil')->nullable();
            
            // FKs (Nullables hasta que complete perfil)
            $table->foreignId('genero_id')->nullable()->constrained('generos');
            $table->foreignId('nacionalidad_id')->nullable()->constrained('nacionalidades');
            $table->foreignId('pais_id')->nullable()->constrained('paises');
            
            // Verificación y Auditoría
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};