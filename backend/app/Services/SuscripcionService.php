<?php

namespace App\Services;

use App\Models\Pago;
use App\Models\Suscripcion;
use App\Models\Usuario;
use Carbon\Carbon;

class SuscripcionService
{
    // ── CONSULTA ─────────────────────────────────────────────

    /**
     * Devuelve (o crea) la suscripcion del usuario.
     *
     * @return array<string, mixed>
     */
    public function obtener(Usuario $usuario): array
    {
        $suscripcion = Suscripcion::firstOrCreate(
            ['id_usuario' => $usuario->id_usuario],
            [
                'plan'             => 0,
                'est_suscripcion'  => 1,
                'fec_inicio'       => now()->toDateString(),
            ]
        );

        return $this->serializar($suscripcion);
    }

    // ── CREAR ORDEN ──────────────────────────────────────────

    /**
     * Crea un registro de pago pendiente y retorna el order_id para confirmar despues.
     *
     * @return array<string, mixed>
     */
    public function registrarOrdenPendiente(Usuario $usuario, string $paypalOrderId, float $monto): array
    {
        $suscripcion = Suscripcion::firstOrCreate(
            ['id_usuario' => $usuario->id_usuario],
            ['plan' => 0, 'est_suscripcion' => 1, 'fec_inicio' => now()->toDateString()]
        );

        $pago = Pago::create([
            'id_suscripcion'  => $suscripcion->id_suscripcion,
            'paypal_order_id' => $paypalOrderId,
            'mon_monto'       => $monto,
            'est_pago'        => 0, // pendiente
            'tipo_pago'       => 'paypal',
        ]);

        return ['pago_id' => $pago->id_pago];
    }

    // ── CONFIRMAR PAGO ────────────────────────────────────────

    /**
     * Marca el pago como completado y activa el plan mensual por 30 dias.
     *
     * @param  array<string, mixed>  $capturaPaypal
     * @return array<string, mixed>
     */
    public function confirmarPago(string $paypalOrderId, array $capturaPaypal): array
    {
        $pago = Pago::where('paypal_order_id', $paypalOrderId)->firstOrFail();

        $pago->update([
            'paypal_transaction_id' => $capturaPaypal['transaction_id'],
            'est_pago'              => 1, // completado
            'fec_pago'              => now()->toDateString(),
        ]);

        $suscripcion = $pago->suscripcion;
        $suscripcion->update([
            'plan'            => 1, // mensual
            'est_suscripcion' => 1, // activo
            'fec_inicio'      => now()->toDateString(),
            'fec_fin'         => now()->addDays(30)->toDateString(),
            'fec_ultimo_pago' => now()->toDateString(),
        ]);

        return $this->serializar($suscripcion->fresh());
    }

    // ── CANCELAR ORDEN ────────────────────────────────────────

    public function cancelarOrden(string $paypalOrderId): void
    {
        $pago = Pago::where('paypal_order_id', $paypalOrderId)->first();
        $pago?->update(['est_pago' => 2]); // cancelado
    }

    // ── SERIALIZAR ────────────────────────────────────────────

    /**
     * @return array<string, mixed>
     */
    private function serializar(Suscripcion $s): array
    {
        return [
            'id_suscripcion'  => $s->id_suscripcion,
            'id_usuario'      => $s->id_usuario,
            'plan'            => $s->plan,
            'est_suscripcion' => $s->est_suscripcion,
            'fec_inicio'      => $s->fec_inicio?->toDateString(),
            'fec_fin'         => $s->fec_fin?->toDateString(),
            'fec_ultimo_pago' => $s->fec_ultimo_pago?->toDateString(),
        ];
    }
}
