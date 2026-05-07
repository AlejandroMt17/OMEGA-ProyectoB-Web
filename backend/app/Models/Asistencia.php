<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    protected $table      = 'asistencias';
    protected $primaryKey = 'id_asistencia';
    public    $timestamps = false;

    protected $fillable = [
        'id_sesion',
        'id_alumno',
        'est_asistencia',
        'hora_registro',
    ];

    protected function casts(): array
    {
        return [
            'hora_registro' => 'datetime',
        ];
    }

    public function sesion()
    {
        return $this->belongsTo(Sesion::class, 'id_sesion', 'id_sesion');
    }

    public function alumno()
    {
        return $this->belongsTo(Usuario::class, 'id_alumno', 'id_usuario');
    }
}