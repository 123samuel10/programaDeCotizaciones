<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Cotizacion;
use App\Models\Producto;
use App\Models\User;
use App\Models\Opcion;

use App\Models\CotizacionItem;
use App\Models\CotizacionItemOpcion;

class CotizacionController extends Controller
{
    // LISTADO
    public function index()
    {
        $cotizaciones = Cotizacion::with(['usuario'])
            ->latest()
            ->get();

        // Mostrar totales correctos (sin “copiar” opciones)
        foreach ($cotizaciones as $c) {
            $this->recalcularTotales($c);
            $c->refresh();
        }

        return view('admin.cotizaciones.index', compact('cotizaciones'));
    }

    // FORM CREAR
    public function create()
    {
        $clientes  = User::where('role', 'cliente')->orderBy('name')->get();
        return view('admin.cotizaciones.create', compact('clientes'));
    }

    // CREAR COTIZACIÓN VACÍA (sin producto fijo)
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $cotizacion = Cotizacion::create([
            'user_id'     => (int) $request->user_id,
            'total_venta' => 0,
            'total_costo' => 0,
        ]);

        return redirect()
            ->route('admin.cotizaciones.edit', $cotizacion->id)
            ->with('success', 'Cotización creada. Ahora agrega productos y adiciones por línea.');
    }

    // EDITAR (agregar ítems y adiciones por ítem)
    public function edit(Cotizacion $cotizacion)
    {
        $cotizacion = Cotizacion::with([
            'usuario',
            'items.producto',
            'items.opciones.opcion',
        ])->findOrFail($cotizacion->id);

        $this->recalcularTotales($cotizacion);
        $cotizacion->refresh();

        $productos = Producto::latest()->get();

        return view('admin.cotizaciones.edit', compact('cotizacion', 'productos'));
    }

    // AGREGAR ITEM (producto + cantidad) a la cotización
    public function agregarItem(Request $request, Cotizacion $cotizacion)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad'    => 'required|integer|min:1|max:999',
        ]);

        $producto = Producto::findOrFail((int)$request->producto_id);

        CotizacionItem::create([
            'cotizacion_id'     => $cotizacion->id,
            'producto_id'       => $producto->id,
            'cantidad'          => (int)$request->cantidad,
            'precio_base_venta' => (float)$producto->precio_base_venta,
            'precio_base_costo' => (float)$producto->precio_base_costo,
        ]);

        $this->recalcularTotales($cotizacion);

        return back()->with('success', 'Producto agregado a la cotización.');
    }

    // ACTUALIZAR CANTIDAD de una línea
    public function actualizarItem(Request $request, Cotizacion $cotizacion, CotizacionItem $item)
    {
        if ($item->cotizacion_id !== $cotizacion->id) abort(403);

        $request->validate([
            'cantidad' => 'required|integer|min:1|max:999',
        ]);

        $item->update([
            'cantidad' => (int)$request->cantidad,
        ]);

        $this->recalcularTotales($cotizacion);

        return back()->with('success', 'Cantidad actualizada.');
    }

    // ELIMINAR ITEM (línea completa)
    public function eliminarItem(Cotizacion $cotizacion, CotizacionItem $item)
    {
        if ($item->cotizacion_id !== $cotizacion->id) abort(403);

        $item->delete();
        $this->recalcularTotales($cotizacion);

        return back()->with('success', 'Línea eliminada.');
    }

    // GREGAR ADICIÓN A UN ITEM ESPECÍFICO
    public function agregarOpcionItem(Request $request, Cotizacion $cotizacion, CotizacionItem $item)
    {
        if ($item->cotizacion_id !== $cotizacion->id) abort(403);

        $request->validate([
            'opcion_id' => 'required|exists:opciones,id',
            'cantidad'  => 'required|integer|min:1|max:99',
        ]);

        // seguridad: opción debe pertenecer al producto de ese item
        $opcion = Opcion::where('id', (int)$request->opcion_id)
            ->where('producto_id', $item->producto_id)
            ->firstOrFail();

        $precio = $opcion->precios()->latest()->first();
        if (!$precio) {
            return back()->with('error', 'Esa opción no tiene precio asignado.');
        }

        $ventaUnit = (float)$precio->precio_venta;
        $costoUnit = (float)$precio->precio_costo;

        $cantidadNueva = (int)$request->cantidad;

        $existente = CotizacionItemOpcion::where('cotizacionitem_id', $item->id)
            ->where('opcion_id', $opcion->id)
            ->first();

        if ($existente) {
            $existente->cantidad += $cantidadNueva;
            $existente->subtotal_venta = $existente->cantidad * $ventaUnit;
            $existente->subtotal_costo = $existente->cantidad * $costoUnit;
            $existente->precio_venta   = $ventaUnit;
            $existente->precio_costo   = $costoUnit;
            $existente->save();
        } else {
            CotizacionItemOpcion::create([
                'cotizacionitem_id' => $item->id,
                'opcion_id'         => $opcion->id,
                'cantidad'          => $cantidadNueva,
                'precio_venta'      => $ventaUnit,
                'precio_costo'      => $costoUnit,
                'subtotal_venta'    => $cantidadNueva * $ventaUnit,
                'subtotal_costo'    => $cantidadNueva * $costoUnit,
            ]);
        }

        $this->recalcularTotales($cotizacion);

        return back()->with('success', 'Adición agregada a esa línea (solo a ese producto).');
    }

    // ELIMINAR ADICIÓN DE UN ITEM
    public function eliminarOpcionItem(Cotizacion $cotizacion, CotizacionItem $item, CotizacionItemOpcion $op)
    {
        if ($item->cotizacion_id !== $cotizacion->id) abort(403);
        if ($op->cotizacionitem_id !== $item->id) abort(403);

        $op->delete();
        $this->recalcularTotales($cotizacion);

        return back()->with('success', 'Adición eliminada.');
    }

    // RECALCULAR TOTALES (SUMA TODAS LAS LÍNEAS + SUS ADICIONES)
    private function recalcularTotales(Cotizacion $cotizacion): void
    {
        $cotizacion->load(['items.opciones']);

        $totalVenta = 0;
        $totalCosto = 0;

        foreach ($cotizacion->items as $it) {
            $baseVenta = (float)$it->precio_base_venta * (int)$it->cantidad;
            $baseCosto = (float)$it->precio_base_costo * (int)$it->cantidad;

            $adVenta = (float)$it->opciones->sum('subtotal_venta');
            $adCosto = (float)$it->opciones->sum('subtotal_costo');

            $totalVenta += ($baseVenta + $adVenta);
            $totalCosto += ($baseCosto + $adCosto);
        }

        $cotizacion->update([
            'total_venta' => $totalVenta,
            'total_costo' => $totalCosto,
        ]);
    }
}
