<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Cotizacion;
use App\Models\Producto;
use App\Models\User;
use App\Models\Opcion;
use App\Models\CotizacionOpcion;

class CotizacionController extends Controller
{
    // LISTADO
    public function index()
    {
        $cotizaciones = Cotizacion::with(['producto', 'usuario'])
            ->withSum('items as adiciones_venta', 'subtotal_venta')
            ->withSum('items as adiciones_costo', 'subtotal_costo')
            ->latest()
            ->get();

        // 👇 NO recalculamos guardando en BD aquí. Solo mostramos.
        return view('admin.cotizaciones.index', compact('cotizaciones'));
    }

    // FORM CREAR
    public function create()
    {
        $clientes = User::where('role', 'cliente')->orderBy('name')->get();
        $productos = Producto::latest()->get();

        return view('admin.cotizaciones.create', compact('clientes', 'productos'));
    }

    // GUARDAR COTIZACIÓN (COPIA OPCIONES DEL PRODUCTO)
    public function store(Request $request)
    {
        $request->validate([
            'user_id'     => 'required|exists:users,id',
            'producto_id' => 'required|exists:productos,id',
        ]);

        $cotizacion = DB::transaction(function () use ($request) {

            $producto = Producto::with([
                'opciones' => fn ($q) => $q->orderBy('orden')->orderBy('nombre'),
                'opciones.precios' => fn ($q) => $q->latest(),
            ])->findOrFail((int) $request->producto_id);

            // 1) Crear cotización
            $cotizacion = Cotizacion::create([
                'user_id'     => (int) $request->user_id,
                'producto_id' => (int) $producto->id,
                'total_venta' => 0,
                'total_costo' => 0,
            ]);

            // 2) Copiar opciones con precio
            foreach ($producto->opciones as $opcion) {
                $precio = $opcion->precios()->latest()->first();
                if (!$precio) {
                    continue;
                }

                $ventaUnit = (float) $precio->precio_venta;
                $costoUnit = (float) $precio->precio_costo;

                CotizacionOpcion::create([
                    'cotizacion_id'  => $cotizacion->id,
                    'opcion_id'      => $opcion->id,
                    'cantidad'       => 1,
                    'subtotal_venta' => $ventaUnit,
                    'subtotal_costo' => $costoUnit,
                ]);
            }

            // 3) Guardar totales ya calculados
            $this->recalcularTotales($cotizacion);

            return $cotizacion;
        });

        return redirect()
            ->route('admin.cotizaciones.edit', $cotizacion->id)
            ->with('success', 'Cotización creada correctamente.');
    }

    public function edit(Cotizacion $cotizacion)
{
    // Fuerza carga real desde BD con relaciones
    $cotizacion = Cotizacion::with(['producto', 'items.opcion', 'usuario'])
        ->findOrFail($cotizacion->id);

    $this->recalcularTotales($cotizacion);
    $cotizacion->refresh();

    $opcionesDisponibles = $cotizacion->producto->opciones()
        ->orderBy('categoria')
        ->orderBy('orden')
        ->orderBy('nombre')
        ->get();

    return view('admin.cotizaciones.edit', compact('cotizacion', 'opcionesDisponibles'));
}

    // AGREGAR ADICIÓN
    public function agregarOpcion(Request $request, Cotizacion $cotizacion)
    {
        $request->validate([
            'opcion_id' => 'required|exists:opciones,id',
            'cantidad'  => 'required|integer|min:1|max:99',
        ]);

        $cotizacion->load('producto');

        if (!$cotizacion->producto) {
            return back()->with('error', 'Cotización dañada: no tiene producto.');
        }

        // Seguridad: opción debe pertenecer al producto
        $opcion = Opcion::where('id', (int) $request->opcion_id)
            ->where('producto_id', $cotizacion->producto_id)
            ->firstOrFail();

        $precio = $opcion->precios()->latest()->first();

        if (!$precio) {
            return back()->with('error', 'Esa opción no tiene precio asignado. Ve a Productos y ponle precio.');
        }

        $ventaUnit = (float) $precio->precio_venta;
        $costoUnit = (float) $precio->precio_costo;

        $cantidadNueva = (int) $request->cantidad;

        $item = CotizacionOpcion::where('cotizacion_id', $cotizacion->id)
            ->where('opcion_id', $opcion->id)
            ->first();

        if ($item) {
            $item->cantidad += $cantidadNueva;
        } else {
            $item = new CotizacionOpcion([
                'cotizacion_id' => $cotizacion->id,
                'opcion_id'     => $opcion->id,
                'cantidad'      => $cantidadNueva,
            ]);
        }

        $item->subtotal_venta = $item->cantidad * $ventaUnit;
        $item->subtotal_costo = $item->cantidad * $costoUnit;
        $item->save();

        $this->recalcularTotales($cotizacion);

        return back()->with('success', 'Adición agregada a la cotización.');
    }

    // ELIMINAR ITEM
    public function eliminarItem(Cotizacion $cotizacion, CotizacionOpcion $item)
    {
        if ($item->cotizacion_id !== $cotizacion->id) {
            abort(403);
        }

        $item->delete();
        $this->recalcularTotales($cotizacion);

        return back()->with('success', 'Adición eliminada.');
    }

    // RECALCULAR TOTALES (BASE + ITEMS)
    private function recalcularTotales(Cotizacion $cotizacion): void
    {
        $cotizacion->load(['items', 'producto']);

        if (!$cotizacion->producto) {
            return;
        }

        $baseVenta = (float) $cotizacion->producto->precio_base_venta;
        $baseCosto = (float) $cotizacion->producto->precio_base_costo;

        $adVenta = (float) $cotizacion->items->sum('subtotal_venta');
        $adCosto = (float) $cotizacion->items->sum('subtotal_costo');

        $cotizacion->update([
            'total_venta' => $baseVenta + $adVenta,
            'total_costo' => $baseCosto + $adCosto,
        ]);
    }
}
