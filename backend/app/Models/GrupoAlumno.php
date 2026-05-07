<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class GrupoAlumno extends Pivot
{
    protected $table      = 'grupo_alumno';
    protected $primaryKey = 'id_grupo_alumno';
    public    $timestamps = false;

    protected $fillable = [
        'id_grupo',
        'id_alumno',
        'fec_inscripcion',
    ];
}