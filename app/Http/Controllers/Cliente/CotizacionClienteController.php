<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Cotizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\VentaItem;
use App\Models\VentaItemOpcion;
use App\Models\Venta;


class CotizacionClienteController extends Controller
{
    // Seguridad SIN middleware
    private function validarCliente()
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Debes iniciar sesión.');
        }

        if ($user->role !== 'cliente') {
            abort(403, 'Acceso solo para clientes.');
        }

        return $user;
    }

    private function validarPropiedad(Cotizacion $cotizacion, $user)
    {
        if ((int)$cotizacion->user_id !== (int)$user->id) {
            abort(403, 'Esta cotización no te pertenece.');
        }
    }

    // Listado / notificaciones
    public function index()
    {
        $user = $this->validarCliente();

        $cotizaciones = Cotizacion::with(['items.producto', 'items.opciones.opcion'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $pendientes = $cotizaciones->where('estado', 'pendiente')->count();

        return view('cliente.cotizaciones.index', compact('cotizaciones', 'pendientes'));
    }

   public function aceptar(Request $request, Cotizacion $cotizacion)
{
    $user = $this->validarCliente();
    $this->validarPropiedad($cotizacion, $user);

    if ($cotizacion->estado !== 'pendiente') {
        return back()->with('error', 'Esta cotización ya fue respondida.');
    }

    $request->validate([
        'nota_cliente' => 'nullable|string|max:1000',
    ]);

    DB::transaction(function () use ($request, $cotizacion, $user) {

        // Cargar todo para copiar
        $cotizacion->load(['items.producto', 'items.opciones.opcion']);

        // 1) marcar cotización como aceptada
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

        // Por si ya existía una venta (por error), limpiamos items para regenerar bien
        VentaItemOpcion::whereIn('ventaitem_id', VentaItem::where('venta_id', $venta->id)->pluck('id'))->delete();
        VentaItem::where('venta_id', $venta->id)->delete();

        // 3) copiar items de la cotización
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

            // 4) copiar opciones de ese item
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

    return back()->with('success', 'Cotización aceptada. La venta fue generada automáticamente.');
}



    // Rechazar
    public function rechazar(Request $request, Cotizacion $cotizacion)
    {
        $user = $this->validarCliente();
        $this->validarPropiedad($cotizacion, $user);

        if ($cotizacion->estado !== 'pendiente') {
            return back()->with('error', 'Esta cotización ya fue respondida.');
        }

        $request->validate([
            'nota_cliente' => 'nullable|string|max:1000',
        ]);

        $cotizacion->update([
            'estado' => 'rechazada',
            'respondida_en' => now(),
            'nota_cliente' => $request->nota_cliente,
        ]);

        return back()->with('success', 'Cotización rechazada correctamente.');
    }
}
