<?php

namespace App\Http\Controllers;

use App\Models\Cotizacion;
use App\Models\Venta;
use App\Models\VentaItem;
use App\Models\VentaItemOpcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf; //

class PublicCotizacionController extends Controller
{
    private function cotizacionPorToken(string $token): Cotizacion
    {
        return Cotizacion::with(['usuario','items.producto','items.opciones.opcion'])
            ->where('token', $token)
            ->firstOrFail();
    }

    public function ver(string $token)
    {
        $cotizacion = $this->cotizacionPorToken($token);
        return view('public.cotizacion', compact('cotizacion'));
    }

    // ✅ Pantalla confirmación
    public function confirmarAceptar(string $token)
    {
        $cotizacion = $this->cotizacionPorToken($token);
        return view('public.confirmar', [
            'cotizacion' => $cotizacion,
            'accion' => 'aceptar',
        ]);
    }

    public function confirmarRechazar(string $token)
    {
        $cotizacion = $this->cotizacionPorToken($token);
        return view('public.confirmar', [
            'cotizacion' => $cotizacion,
            'accion' => 'rechazar',
        ]);
    }

    // ✅ Acción real: ACEPTAR (crea venta y copia todo)
    public function aceptar(Request $request, string $token)
    {
        $cotizacion = $this->cotizacionPorToken($token);

        if (($cotizacion->estado ?? 'pendiente') !== 'pendiente') {
            return view('public.resultado', [
                'titulo' => 'Esta cotización ya fue respondida',
                'mensaje' => 'Estado: ' . strtoupper($cotizacion->estado),
            ]);
        }

        $request->validate([
            'nota_cliente' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($request, $cotizacion) {

            $cotizacion->load(['items.producto', 'items.opciones.opcion']);
            $user = $cotizacion->usuario; // ✅ dueño real

            // 1) marcar cotización aceptada
            $cotizacion->update([
                'estado' => 'aceptada',
                'respondida_en' => now(),
                'nota_cliente' => $request->nota_cliente,
            ]);

            // 2) crear venta (1 por cotización)
            $venta = Venta::updateOrCreate(
                ['cotizacion_id' => $cotizacion->id],
                [
                    'user_id'      => $user->id,
                    'total_venta'  => (float) $cotizacion->total_venta,
                    'total_costo'  => (float) $cotizacion->total_costo,
                    'estado_venta' => 'pendiente_pago',
                    'nota_cliente' => $request->nota_cliente,
                ]
            );

            // limpiar por si ya existía
            VentaItemOpcion::whereIn('ventaitem_id', VentaItem::where('venta_id', $venta->id)->pluck('id'))->delete();
            VentaItem::where('venta_id', $venta->id)->delete();

            // 3) copiar items
            foreach ($cotizacion->items as $it) {
                $p = $it->producto;

                $subtotalVenta = ((float)$it->precio_base_venta * (int)$it->cantidad) + (float)$it->opciones->sum('subtotal_venta');
                $subtotalCosto = ((float)$it->precio_base_costo * (int)$it->cantidad) + (float)$it->opciones->sum('subtotal_costo');

                $ventaItem = VentaItem::create([
                    'venta_id'          => $venta->id,
                    'producto_id'       => $it->producto_id,
                    'nombre_producto'   => $p->nombre_producto ?? null,
                    'marca'             => $p->marca ?? null,
                    'modelo'            => $p->modelo ?? null,
                    'cantidad'          => (int)$it->cantidad,
                    'precio_unit_venta' => (float)$it->precio_base_venta,
                    'precio_unit_costo' => (float)$it->precio_base_costo,
                    'subtotal_venta'    => $subtotalVenta,
                    'subtotal_costo'    => $subtotalCosto,
                ]);

                // 4) copiar opciones
                foreach ($it->opciones as $io) {
                    VentaItemOpcion::create([
                        'ventaitem_id'      => $ventaItem->id,
                        'opcion_id'         => $io->opcion_id,
                        'nombre_opcion'     => $io->opcion->nombre ?? null,
                        'cantidad'          => (int)$io->cantidad,
                        'precio_unit_venta' => (float)$io->precio_venta,
                        'precio_unit_costo' => (float)$io->precio_costo,
                        'subtotal_venta'    => (float)$io->subtotal_venta,
                        'subtotal_costo'    => (float)$io->subtotal_costo,
                    ]);
                }
            }
        });

        return view('public.resultado', [
            'titulo' => '¡Cotización aceptada!',
            'mensaje' => 'Gracias. La venta fue generada automáticamente.',
        ]);
    }

    // ✅ Acción real: RECHAZAR
    public function rechazar(Request $request, string $token)
    {
        $cotizacion = $this->cotizacionPorToken($token);

        if (($cotizacion->estado ?? 'pendiente') !== 'pendiente') {
            return view('public.resultado', [
                'titulo' => 'Esta cotización ya fue respondida',
                'mensaje' => 'Estado: ' . strtoupper($cotizacion->estado),
            ]);
        }

        $request->validate([
            'nota_cliente' => 'nullable|string|max:1000',
        ]);

        $cotizacion->update([
            'estado' => 'rechazada',
            'respondida_en' => now(),
            'nota_cliente' => $request->nota_cliente,
        ]);

        return view('public.resultado', [
            'titulo' => 'Cotización rechazada',
            'mensaje' => 'Gracias por tu respuesta.',
        ]);
    }
    public function pdf(string $token)
{
    $cotizacion = $this->cotizacionPorToken($token);

    // Vista especial SOLO para PDF (sin layout web)
    $pdf = Pdf::loadView('public.cotizacion-pdf', [
        'cotizacion' => $cotizacion
    ])->setPaper('a4');

    $nombre = 'Cotizacion_'.$cotizacion->id.'.pdf';

    return $pdf->download($nombre); // fuerza descarga
    // Si quisieras verlo en el navegador: return $pdf->stream($nombre);
}
}
