<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table      = 'pagos';
    protected $primaryKey = 'id_pago';
    public    $timestamps = false;

    protected $fillable = [
        'id_suscripcion',
        'paypal_order_id',
        'paypal_transaction_id',
        'mon_monto',
        'est_pago',
        'fec_pago',
        'tipo_pago',
    ];

    protected function casts(): array
    {
        return [
            'fec_pago'  => 'date',
            'mon_monto' => 'decimal:2',
        ];
    }

    public function suscripcion()
    {
        return $this->belongsTo(Suscripcion::class, 'id_suscripcion', 'id_suscripcion');
    }
}