<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suscripcion extends Model
{
    protected $table      = 'suscripciones';
    protected $primaryKey = 'id_suscripcion';
    public    $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'plan',
        'est_suscripcion',
        'fec_inicio',
        'fec_fin',
        'fec_ultimo_pago',
    ];

    protected function casts(): array
    {
        return [
            'fec_inicio'      => 'date',
            'fec_fin'         => 'date',
            'fec_ultimo_pago' => 'date',
        ];
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'id_suscripcion', 'id_suscripcion');
    }
}