<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Institucion extends Model
{
    protected $table      = 'instituciones';
    protected $primaryKey = 'id_institucion';
    public    $timestamps = false;

    protected $fillable = [
        'id_docente',
        'nombre',
        'logo',
    ];

    public function getRouteKeyName(): string
    {
        return 'id_institucion';
    }

    public function docente()
    {
        return $this->belongsTo(Usuario::class, 'id_docente', 'id_usuario');
    }

    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'id_institucion', 'id_institucion');
    }

    public function rubros()
    {
        return $this->hasMany(RubroEvaluacion::class, 'id_institucion', 'id_institucion');
    }
}