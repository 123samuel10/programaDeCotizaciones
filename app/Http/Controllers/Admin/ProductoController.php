<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Producto;
class ProductoController extends Controller
{
 // Mostrar lista de productos
    public function index()
    {
        $productos = Producto::all();
        return view('admin.productos.index', compact('productos'));
    }

    // Mostrar formulario para crear un producto
    public function create()
    {
       // Listas de opciones
    $marcas = ['LG', 'Samsung', 'Whirlpool', 'Mabe'];
    $tipos = ['Nevera', 'Congelador', 'Microondas', 'Horno'];
    $colores = ['Blanco', 'Negro', 'Acero inoxidable', 'Gris'];
    $capacidades = ['100 litros', '200 litros', '300 litros', '500 litros'];

    // Retornar la vista con todas las variables
    return view('admin.productos.create', compact('marcas', 'tipos', 'colores', 'capacidades'));
    }

    // Guardar nuevo producto en la base de datos
    public function store(Request $request)
    {
     $request->validate([
    'nombre' => 'required|string|max:255',
    'marca' => 'required|string|max:255',
    'modelo' => 'nullable|string|max:255',
    'tipo' => 'required|string|max:255',        // acepta todas las opciones
    'capacidad' => 'required|string|max:50',    // string porque viene con "litros"
    'peso' => 'required|string|max:50',         // string, para evitar error si escriben kg
    'dimensiones' => 'nullable|string|max:255',
    'color' => 'required|string|max:50',
    'precio' => 'required|numeric',
    'stock' => 'required|integer',
]);

        Producto::create($request->all());

        return redirect()->route('admin.productos.index')->with('success', 'Producto creado correctamente.');
    }

    // Mostrar un producto específico
    public function show(Producto $producto)
    {
        return view('admin.productos.show', compact('producto'));
    }

    // Mostrar formulario para editar un producto
    public function edit(Producto $producto)
    {
        return view('admin.productos.edit', compact('producto'));
    }

    // Actualizar un producto
    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'marca' => 'required|string|max:255',
            'modelo' => 'required|string|max:255',
            'tipo' => 'required|in:Nevera,Congelador',
            'capacidad' => 'required|integer',
            'peso' => 'required|numeric',
            'dimensiones' => 'required|string|max:255',
            'color' => 'required|string|max:50',
            'precio' => 'required|numeric',
            'stock' => 'required|integer',
        ]);

        $producto->update($request->all());

        return redirect()->route('admin.productos.index')->with('success', 'Producto actualizado correctamente.');
    }

    // Eliminar un producto
    public function destroy(Producto $producto)
    {
        $producto->delete();
        return redirect()->route('admin.productos.index')->with('success', 'Producto eliminado correctamente.');
    }
}
