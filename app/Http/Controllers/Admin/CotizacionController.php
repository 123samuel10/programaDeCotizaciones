<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Mail\CotizacionMail;

use App\Models\Cotizacion;
use App\Models\Producto;
use App\Models\User;
use App\Models\Opcion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Models\CotizacionItem;
use App\Models\CotizacionItemOpcion;

class CotizacionController extends Controller
{
  // Seguridad SIN middleware
    private function validarAdmin()
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Debes iniciar sesión.');
        }

        if (($user->role ?? null) !== 'admin') {
            abort(403, 'Acceso solo para administradores.');
        }

        return $user;
    }

    // Bloqueo si el cliente ya respondió
    private function asegurarPendiente(Cotizacion $cotizacion): void
    {
        $estado = $cotizacion->estado ?? 'pendiente';
        if ($estado !== 'pendiente') {
            abort(403, 'Esta cotización ya fue respondida por el cliente y está bloqueada.');
        }
    }
 public function index()
{
    $this->validarAdmin();

    $cotizaciones = Cotizacion::with(['usuario'])
        ->withCount('items')
        ->latest()
        ->paginate(15)          // 👈 cantidad por página (15, 20, 25...)
        ->withQueryString();    // 👈 conserva parámetros (ej: page)

    return view('admin.cotizaciones.index', compact('cotizaciones'));
}



    // FORM CREAR
    public function create()
    {
        $this->validarAdmin();

        $clientes = User::where('role', 'cliente')->orderBy('name')->get();
        return view('admin.cotizaciones.create', compact('clientes'));
    }

    // CREAR COTIZACIÓN VACÍA
    public function store(Request $request)
    {
        $this->validarAdmin();

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $cotizacion = Cotizacion::create([
            'user_id'     => (int) $request->user_id,
            'total_venta' => 0,
            'total_costo' => 0,
            'estado'      => 'pendiente',
        ]);

        return redirect()
            ->route('admin.cotizaciones.edit', $cotizacion->id)
            ->with('success', 'Cotización creada. Ahora agrega productos y adiciones por línea.');
    }

    // EDITAR
  public function edit(Cotizacion $cotizacion)
{
    $this->validarAdmin();

    $cotizacion = Cotizacion::with([
        'usuario',
        'items.producto',
        'items.opciones.opcion',
    ])->findOrFail($cotizacion->id);

    $this->recalcularTotales($cotizacion);
    $cotizacion->refresh();

    $productos = Producto::orderBy('marca')
        ->orderBy('modelo')
        ->orderBy('nombre_producto')
        ->get([
            'id',
            'marca',
            'modelo',
            'nombre_producto',
            'descripcion',
            'foto',
            'repisas_iluminadas',
            'refrigerante',
            'longitud',
            'profundidad',
            'altura',
            'precio_base_venta',
            'precio_base_costo',
        ]);

    $productosAgregadosIds = $cotizacion->items->pluck('producto_id')->unique()->toArray();

    return view('admin.cotizaciones.edit', compact('cotizacion', 'productos', 'productosAgregadosIds'));
}


    // AGREGAR ITEM
    // AGREGAR ITEM
public function agregarItem(Request $request, Cotizacion $cotizacion)
{
    $this->validarAdmin();
    $this->asegurarPendiente($cotizacion);

    $request->validate([
        'producto_id' => 'required|exists:productos,id',
        'cantidad'    => 'required|integer|min:1|max:999',
    ]);

    $productoId = (int) $request->producto_id;

    // Regla PRO: si ya existe esa línea, NO se crea otra. Se actualiza la cantidad.
    $itemExistente = CotizacionItem::where('cotizacion_id', $cotizacion->id)
        ->where('producto_id', $productoId)
        ->first();

    if ($itemExistente) {
        $itemExistente->cantidad += (int) $request->cantidad;
        $itemExistente->save();

        $this->recalcularTotales($cotizacion);

        return back()->with('success', 'Ese producto ya estaba agregado. Se sumó la cantidad en la misma línea.');
    }

    $producto = Producto::findOrFail($productoId);

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


    // ACTUALIZAR CANTIDAD
    public function actualizarItem(Request $request, Cotizacion $cotizacion, CotizacionItem $item)
    {
        $this->validarAdmin();
        $this->asegurarPendiente($cotizacion);

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

    // ELIMINAR ITEM
    public function eliminarItem(Cotizacion $cotizacion, CotizacionItem $item)
    {
        $this->validarAdmin();
        $this->asegurarPendiente($cotizacion);

        if ($item->cotizacion_id !== $cotizacion->id) abort(403);

        $item->delete();
        $this->recalcularTotales($cotizacion);

        return back()->with('success', 'Línea eliminada.');
    }

    // AGREGAR ADICIÓN A ITEM
    public function agregarOpcionItem(Request $request, Cotizacion $cotizacion, CotizacionItem $item)
    {
        $this->validarAdmin();
        $this->asegurarPendiente($cotizacion);

        if ($item->cotizacion_id !== $cotizacion->id) abort(403);

        $request->validate([
            'opcion_id' => 'required|exists:opciones,id',
            'cantidad'  => 'required|integer|min:1|max:99',
        ]);

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

    // ELIMINAR ADICIÓN DE ITEM
    public function eliminarOpcionItem(Cotizacion $cotizacion, CotizacionItem $item, CotizacionItemOpcion $op)
    {
        $this->validarAdmin();
        $this->asegurarPendiente($cotizacion);

        if ($item->cotizacion_id !== $cotizacion->id) abort(403);
        if ($op->cotizacionitem_id !== $item->id) abort(403);

        $op->delete();
        $this->recalcularTotales($cotizacion);

        return back()->with('success', 'Adición eliminada.');
    }

    // RECALCULAR TOTALES
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

    // ELIMINAR COTIZACIÓN COMPLETA
    public function destroy(Cotizacion $cotizacion)
    {
        $this->validarAdmin();

        DB::transaction(function () use ($cotizacion) {
            CotizacionItemOpcion::whereIn(
                'cotizacionitem_id',
                CotizacionItem::where('cotizacion_id', $cotizacion->id)->pluck('id')
            )->delete();

            CotizacionItem::where('cotizacion_id', $cotizacion->id)->delete();

            $cotizacion->delete();
        });

        return redirect()
            ->route('admin.cotizaciones.index')
            ->with('success', 'Cotización eliminada correctamente.');
    }

public function enviarPorCorreo(Cotizacion $cotizacion)
{
    $this->validarAdmin();

    // Asegurar relación usuario
    $cotizacion->load('usuario');

    // 🔐 Generar token si no existe
    if (empty($cotizacion->token)) {
        $cotizacion->token = Str::random(64); // coincide con tu migración token(64)
        $cotizacion->save();
    }

    Mail::to($cotizacion->usuario->email)
        ->send(new CotizacionMail($cotizacion));

    return back()->with('success', 'Cotización enviada por correo correctamente.');
}



}
