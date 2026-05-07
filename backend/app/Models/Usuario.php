<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table      = 'usuarios';
    protected $primaryKey = 'id_usuario';
    public    $timestamps = false;

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

    protected function casts(): array
    {
        return [
            'contrasenia' => 'hashed',
        ];
    }

    public function getAuthPassword(): string
    {
        return $this->contrasenia;
    }

    public function isDocente(): bool
    {
        return $this->rol === 1;
    }

    public function isAlumno(): bool
    {
        return $this->rol === 2;
    }

    public function instituciones()
    {
        return $this->hasMany(Institucion::class, 'id_docente', 'id_usuario');
    }

    public function suscripcion()
    {
        return $this->hasOne(Suscripcion::class, 'id_usuario', 'id_usuario');
    }

    public function grupos()
    {
        return $this->belongsToMany(
            Grupo::class,
            'grupo_alumno',
            'id_alumno',
            'id_grupo'
        )->withPivot('fec_inscripcion');
    }
}