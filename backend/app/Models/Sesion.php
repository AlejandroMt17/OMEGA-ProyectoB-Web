<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sesion extends Model
{
    protected $table      = 'sesiones';
    protected $primaryKey = 'id_sesion';
    public    $timestamps = false;

    protected $fillable = [
        'id_grupo',
        'est_sesion',
        'fec_sesion',
        'hora_apertura',
        'hora_cierre',
        'clave',
    ];

    protected function casts(): array
    {
        return [
            'hora_apertura' => 'datetime',
            'hora_cierre'   => 'datetime',
            'fec_sesion'    => 'date',
        ];
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'id_grupo', 'id_grupo');
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'id_sesion', 'id_sesion');
    }

    public function isAbierta(): bool
    {
        return $this->est_sesion === 1;
    }
}