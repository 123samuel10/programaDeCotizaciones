<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Producto;
use App\Models\Opcion;
use App\Models\OpcionPrecio;
use App\Models\Cotizacion; // para validar si se puede eliminar
use App\Models\CotizacionItem;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::latest()->get();
        return view('admin.productos.index', compact('productos'));
    }

    public function create()
    {
        return view('admin.productos.create');
    }

   public function store(Request $request)
{
    $request->validate([
        'marca' => 'required|string|max:255',
        'modelo' => 'required|string|max:255',
        'nombre_producto' => 'required|string|max:255',
        'repisas_iluminadas' => 'nullable|integer|min:0|max:20',
        'precio_base_venta' => 'required|numeric',
        'precio_base_costo' => 'required|numeric',

        //  FOTO
        'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
    ]);

    $data = $request->only([
        'marca','modelo','nombre_producto','descripcion',
        'repisas_iluminadas','refrigerante','longitud','profundidad','altura',
        'precio_base_venta','precio_base_costo'
    ]);

    // Guardar foto en storage/public/productos
    if ($request->hasFile('foto')) {
        $data['foto'] = $request->file('foto')->store('productos', 'public');
    }

    $producto = Producto::create($data);

    return redirect()->route('admin.productos.edit', $producto)
        ->with('success', 'Producto creado correctamente');
}

    public function edit(Producto $producto)
    {
        $producto->load(['opciones.precios' => function ($q) {
            $q->latest();
        }]);

        $categorias = [
            'Accesorios',
            'Refrigeración',
            'CO2-Control',
            'Logística',
            'Otros',
        ];

        $opcionesDisponibles = [
         'Accesorios' => [
    'ESPEJO SUPERIOR FRUVER Y CARNES',
    'REPISA ADICIONAL',
],
            'Refrigeración' => [
                'EVAPORADOR PARA CARNES CON DESHIELO ELÉCTRICO',
                'R290 EVAP PRESURIZADO',
                'R290 UNIDAD CONDENSADORA AIRE',
                'R290 UNIDAD CONDENSADORA AGUA',
            ],
            'CO2-Control' => [
                'CO2 EVAP PRESURIZADO',
                'CO2 EVAP CAREL (EEV DRIVER, CONTROL Y SENSORES)',
                'CO2 EVAP DANFOSS (EEV DRIVER, CONTROL Y SENSORES)',
            ],
            'Logística' => [
                'SKD (SEMI-KNOCKED DOWN)',
            ],
        ];

        return view('admin.productos.edit', compact(
            'producto',
            'categorias',
            'opcionesDisponibles'
        ));
    }

    public function agregarOpcion(Request $request, Producto $producto)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'precio_venta' => 'required|numeric',
            'precio_costo' => 'required|numeric',
        ]);

        $nombre = trim(preg_replace('/\s+/', ' ', $request->nombre));

        $opcion = Opcion::firstOrCreate(
            ['producto_id' => $producto->id, 'nombre' => $nombre],
            ['categoria' => $request->categoria ?? 'Adiciones']
        );

        $precio = $opcion->precios()->latest()->first();

        if ($precio) {
            $precio->update([
                'precio_venta' => $request->precio_venta,
                'precio_costo' => $request->precio_costo,
            ]);
        } else {
            $opcion->precios()->create([
                'precio_venta' => $request->precio_venta,
                'precio_costo' => $request->precio_costo,
            ]);
        }

        return back()->with('success', 'Adición guardada / actualizada.');
    }

    public function togglePredeterminada(Request $request, Producto $producto, Opcion $opcion)
    {
        if ($opcion->producto_id !== $producto->id) {
            abort(403);
        }

        $opcion->update([
            'es_predeterminada' => (bool) $request->input('es_predeterminada', false),
        ]);

        return back()->with('success', 'Predeterminada actualizada.');
    }

 public function destroy(Producto $producto)
    {
        $tieneItems = CotizacionItem::where('producto_id', $producto->id)->exists();

        if ($tieneItems) {
            return back()->with('error', 'No puedes eliminar este producto porque ya está usado en cotizaciones.');
        }

        // borrar foto si existe
        if ($producto->foto && Storage::disk('public')->exists($producto->foto)) {
            Storage::disk('public')->delete($producto->foto);
        }

        $producto->delete();

        return redirect()
            ->route('admin.productos.index')
            ->with('success', 'Producto eliminado correctamente.');
    }

  public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'marca' => 'required|string|max:255',
            'modelo' => 'required|string|max:255',
            'nombre_producto' => 'required|string|max:255',
            'repisas_iluminadas' => 'nullable|integer|min:0|max:20',
            'precio_base_venta' => 'required|numeric',
            'precio_base_costo' => 'required|numeric',

            // FOTO
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $data = $request->only([
            'marca','modelo','nombre_producto','descripcion',
            'repisas_iluminadas','refrigerante','longitud','profundidad','altura',
            'precio_base_venta','precio_base_costo'
        ]);

        if ($request->hasFile('foto')) {
            if ($producto->foto && Storage::disk('public')->exists($producto->foto)) {
                Storage::disk('public')->delete($producto->foto);
            }
            $data['foto'] = $request->file('foto')->store('productos', 'public');
        }

        $producto->update($data);

        return back()->with('success', 'Producto actualizado correctamente.');
    }

}
