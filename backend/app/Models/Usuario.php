<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Modelo Eloquent — tabla: usuarios
 * Roles: 1 = Docente, 2 = Alumno
 */
class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'nombre',
        'ap_pat',
        'ap_mat',
        'email',
        'contrasenia',
        'rol',
    ];

    protected $hidden = [
        'contrasenia',
    ];

    // Alias para que Laravel Auth use 'contrasenia' como 'password'
    public function getAuthPassword(): string
    {
        return $this->contrasenia;
    }

    protected function casts(): array
    {
        return [
            'rol'         => 'integer',
            'contrasenia' => 'hashed',
        ];
    }

    // Relaciones
    public function instituciones()
    {
        return $this->hasMany(Institucion::class, 'id_docente', 'id_usuario');
    }

    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'id_docente', 'id_usuario');
    }

    public function suscripcion()
    {
        return $this->hasOne(Suscripcion::class, 'id_usuario', 'id_usuario');
    }

    public function grupoAlumnos()
    {
        return $this->hasMany(GrupoAlumno::class, 'id_alumno', 'id_usuario');
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'id_alumno', 'id_usuario');
    }
}