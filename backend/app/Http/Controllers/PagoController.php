<?php

namespace App\Http\Controllers;

use App\Services\PayPalService;
use App\Services\SuscripcionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class PagoController extends Controller
{
    public function __construct(
        private readonly PayPalService      $paypal,
        private readonly SuscripcionService $suscripciones,
    ) {}

    // POST /pagos/paypal/crear-orden
    public function crearOrden(Request $request): JsonResponse
    {
        Log::info('[PayPal] crearOrden alcanzado — usuario: ' . $request->user()?->id_usuario);
        try {
            $monto  = 149.00; // Plan Mensual fijo por ahora
            $result = $this->paypal->crearOrden($monto);

            // Registrar pago pendiente en BD
            $this->suscripciones->registrarOrdenPendiente(
                $request->user(),
                $result['order_id'],
                $monto,
            );

            return response()->json([
                'data' => [
                    'order_id'     => $result['order_id'],
                    'approval_url' => $result['approval_url'],
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('[PayPal] crearOrden falló: ' . get_class($e) . ' — ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 502);
        }
    }

    // POST /pagos/paypal/confirmar
    public function confirmar(Request $request): JsonResponse
    {
        $request->validate([
            'order_id' => ['required', 'string'],
        ]);

        Log::info('[PayPal] confirmar order_id: ' . $request->input('order_id'));

        try {
            $captura     = $this->paypal->capturarOrden($request->input('order_id'));
            Log::info('[PayPal] captura OK: ' . json_encode($captura));
            $suscripcion = $this->suscripciones->confirmarPago(
                $request->input('order_id'),
                $captura,
            );

            return response()->json(['data' => $suscripcion]);
        } catch (\Throwable $e) {
            $clase   = get_class($e);
            $mensaje = $e->getMessage();
            Log::error("[PayPal] confirmar falló: {$clase} — {$mensaje}");
            return response()->json([
                'message' => "[{$clase}] {$mensaje}",
            ], 502);
        }
    }

    // POST /pagos/paypal/cancelar
    public function cancelar(Request $request): JsonResponse
    {
        $request->validate([
            'order_id' => ['required', 'string'],
        ]);

        $this->suscripciones->cancelarOrden($request->input('order_id'));

        return response()->json(['message' => 'Orden cancelada.']);
    }
}
