<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RubroEvaluacion extends Model
{
    protected $table      = 'rubros_evaluacion';
    protected $primaryKey = 'id_rubro';
    public    $timestamps = false;

    protected $fillable = [
        'id_institucion',
        'nombre',
        'porcentaje_minimo',
    ];

    public function institucion()
    {
        return $this->belongsTo(Institucion::class, 'id_institucion', 'id_institucion');
    }
}