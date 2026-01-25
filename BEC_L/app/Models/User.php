<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'alias',
        'email',
        'password',
        'rol_id',
        'nombre',
        'edad',
        'telefono',
        'genero_id',
        'nacionalidad_id',
        'pais_id',
        'foto_perfil',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $appends = ['profile_completeness']; // Para que se serialice si lo pasas a JSON

    // --- RELACIONES ---
    
    public function rol()
    {
        return $this->belongsTo(Role::class, 'rol_id');
    }

    public function direccion()
    {
        return $this->hasOne(Address::class, 'usuario_id');
    }

    // --- LOGICA DE NEGOCIO ---

    /**
     * Calcula el porcentaje de completitud del perfil.
     * Se basa en los campos personales básicos.
     */
    public function getProfileCompletenessAttribute()
    {
        $fields = ['nombre', 'alias', 'edad', 'genero_id', 'nacionalidad_id', 'pais_id'];
        $total = count($fields);
        $filled = 0;

        foreach ($fields as $field) {
            if (!empty($this->$field)) {
                $filled++;
            }
        }

        return round(($filled / $total) * 100);
    }
}