<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    protected $table      = 'grupos';
    protected $primaryKey = 'id_grupo';
    public    $timestamps = false;

    protected $fillable = [
        'id_institucion',
        'nombre',
        'materia',
        'periodo',
        'no_alumnos',
        'codigo_inv',
    ];

    public function institucion()
    {
        return $this->belongsTo(Institucion::class, 'id_institucion', 'id_institucion');
    }

    public function alumnos()
    {
        return $this->belongsToMany(
            Usuario::class,
            'grupo_alumno',
            'id_grupo',
            'id_alumno'
        )->withPivot('fec_inscripcion');
    }

    public function sesiones()
    {
        return $this->hasMany(Sesion::class, 'id_grupo', 'id_grupo');
    }

    public function sesionActiva()
    {
        return $this->hasOne(Sesion::class, 'id_grupo', 'id_grupo')
            ->where('est_sesion', 1);
    }

    public function getRouteKeyName(): string
    {
        return 'id_grupo';
    }
}