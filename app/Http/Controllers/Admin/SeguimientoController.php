<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Seguimiento;
use App\Models\Proveedor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Contenedor;
use App\Models\SeguimientoEvento;

class SeguimientoController extends Controller
{
    private function validarAdmin()
    {
        $u = Auth::user();
        if (!$u) abort(403, 'Debes iniciar sesión.');
        if (($u->role ?? null) !== 'admin') abort(403, 'Acceso solo para administradores.');
        return $u;
    }

    public function index(Request $request)
    {
        $this->validarAdmin();

        $q = trim((string) $request->get('q', ''));
        $estado = $request->get('estado');

        $seguimientos = Seguimiento::with(['venta.usuario', 'proveedor'])
            ->when($q, function($query) use ($q){
                $query->whereHas('venta', function($v) use ($q){
                    $v->where('id', $q)
                      ->orWhereHas('usuario', function($u) use ($q){
                          $u->where('name', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%")
                            ->orWhere('empresa', 'like', "%{$q}%");
                      });
                });
            })
            ->when($estado, fn($query) => $query->where('estado', $estado))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $estados = $this->estados();

        return view('admin.seguimientos.index', compact('seguimientos', 'q', 'estado', 'estados'));
    }

    // Crear/abrir seguimiento desde una venta
    public function createFromVenta(Venta $venta)
    {
        $this->validarAdmin();

        $venta->load(['usuario', 'seguimiento']);

        // Si ya existe, redirecciona al show
        if ($venta->seguimiento) {
            return redirect()->route('admin.seguimientos.show', $venta->seguimiento->id);
        }

        $proveedores = Proveedor::orderBy('nombre')->get();
        $estados = $this->estados();

        return view('admin.seguimientos.create', compact('venta', 'proveedores', 'estados'));
    }

    public function storeFromVenta(Request $request, Venta $venta)
    {
        $this->validarAdmin();

        // Evitar duplicado
        if ($venta->seguimiento()->exists()) {
            return redirect()->route('admin.seguimientos.show', $venta->seguimiento->id)
                ->with('success', 'Este seguimiento ya existía.');
        }

        $data = $request->validate([
            'proveedor_id' => 'nullable|exists:proveedores,id',
            'pais_destino' => 'nullable|string|max:120',
            'tipo_envio' => 'required|in:maritimo,aereo',
            'incoterm' => 'nullable|string|max:50',
            'estado' => 'required|string|max:50',
            'etd' => 'nullable|date',
            'eta' => 'nullable|date',
            'observaciones' => 'nullable|string|max:5000',
        ]);

        $data['venta_id'] = $venta->id;

        $seguimiento = Seguimiento::create($data);

        // Evento automático inicial (PRO)
        SeguimientoEvento::create([
            'seguimiento_id' => $seguimiento->id,
            'creado_por' => Auth::id(),
            'tipo' => 'general',
            'titulo' => 'Seguimiento creado',
            'descripcion' => 'Se creó el seguimiento para esta venta.',
            'fecha_evento' => now(),
        ]);

        return redirect()->route('admin.seguimientos.show', $seguimiento->id)
            ->with('success', 'Seguimiento creado correctamente.');
    }

    public function show(Seguimiento $seguimiento)
    {
        $this->validarAdmin();

        $seguimiento->load([
            'venta.usuario',
            'proveedor',
            'contenedores',
            'eventos.creador',
        ]);

        $proveedores = Proveedor::orderBy('nombre')->get();
        $estados = $this->estados();
        $estadosContenedor = $this->estadosContenedor();

        return view('admin.seguimientos.show', compact(
            'seguimiento', 'proveedores', 'estados', 'estadosContenedor'
        ));
    }

    public function update(Request $request, Seguimiento $seguimiento)
    {
        $this->validarAdmin();

        $data = $request->validate([
            'proveedor_id' => 'nullable|exists:proveedores,id',
            'pais_destino' => 'nullable|string|max:120',
            'tipo_envio' => 'required|in:maritimo,aereo',
            'incoterm' => 'nullable|string|max:50',
            'estado' => 'required|string|max:50',
            'etd' => 'nullable|date',
            'eta' => 'nullable|date',
            'observaciones' => 'nullable|string|max:5000',
        ]);

        $cambioEstado = $seguimiento->estado !== $data['estado'];

        $seguimiento->update($data);

        if ($cambioEstado) {
            SeguimientoEvento::create([
                'seguimiento_id' => $seguimiento->id,
                'creado_por' => Auth::id(),
                'tipo' => 'general',
                'titulo' => 'Cambio de estado',
                'descripcion' => 'Nuevo estado: ' . $data['estado'],
                'fecha_evento' => now(),
            ]);
        }

        return back()->with('success', 'Seguimiento actualizado.');
    }

    // -------- CONTENEDORES --------
    public function contenedorStore(Request $request, Seguimiento $seguimiento)
    {
        $this->validarAdmin();

        $data = $request->validate([
            'numero_contenedor' => 'nullable|string|max:50',
            'bl' => 'nullable|string|max:80',
            'naviera' => 'nullable|string|max:120',
            'puerto_salida' => 'nullable|string|max:120',
            'puerto_llegada' => 'nullable|string|max:120',
            'etd' => 'nullable|date',
            'eta' => 'nullable|date',
            'estado' => 'required|string|max:50',
        ]);

        $data['seguimiento_id'] = $seguimiento->id;

        Contenedor::create($data);

        SeguimientoEvento::create([
            'seguimiento_id' => $seguimiento->id,
            'creado_por' => Auth::id(),
            'tipo' => 'embarque',
            'titulo' => 'Contenedor agregado',
            'descripcion' => 'Se registró un contenedor en el seguimiento.',
            'fecha_evento' => now(),
        ]);

        return back()->with('success', 'Contenedor agregado.');
    }

    public function contenedorDestroy(Seguimiento $seguimiento, Contenedor $contenedor)
    {
        $this->validarAdmin();
        if ($contenedor->seguimiento_id !== $seguimiento->id) abort(403);

        $contenedor->delete();

        return back()->with('success', 'Contenedor eliminado.');
    }

    // -------- EVENTOS --------
    public function eventoStore(Request $request, Seguimiento $seguimiento)
    {
        $this->validarAdmin();

        $data = $request->validate([
            'tipo' => 'required|string|max:50',
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:5000',
            'fecha_evento' => 'nullable|date',
            'archivo' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
        ]);

        $archivoPath = null;
        if ($request->hasFile('archivo')) {
            $archivoPath = $request->file('archivo')->store('seguimientos', 'public');
        }

        SeguimientoEvento::create([
            'seguimiento_id' => $seguimiento->id,
            'creado_por' => Auth::id(),
            'tipo' => $data['tipo'],
            'titulo' => $data['titulo'],
            'descripcion' => $data['descripcion'] ?? null,
            'fecha_evento' => $data['fecha_evento'] ?? now(),
            'archivo' => $archivoPath,
        ]);

        return back()->with('success', 'Evento agregado al seguimiento.');
    }

    public function eventoDestroy(Seguimiento $seguimiento, SeguimientoEvento $evento)
    {
        $this->validarAdmin();
        if ($evento->seguimiento_id !== $seguimiento->id) abort(403);

        if (!empty($evento->archivo) && Storage::disk('public')->exists($evento->archivo)) {
            Storage::disk('public')->delete($evento->archivo);
        }

        $evento->delete();

        return back()->with('success', 'Evento eliminado.');
    }

    // -------- Helpers --------
  private function estados(): array
{
    return [
        'ordencompra'      => 'Compra confirmada',
        'produccion'       => 'En fabricación',
        'listoparaembarque'=> 'Listo para despachar',
        'embarcado'        => 'Despachado / Embarcado',
        'entransito'       => 'En camino (Tránsito)',
        'arriboapuerto'    => 'Llegó al puerto',
        'aduana'           => 'En revisión de aduana',
        'liberado'         => 'Liberado (listo para entrega)',
        'entregado'        => 'Entregado al cliente',
        'cerrado'          => 'Caso cerrado',
    ];
}


    private function estadosContenedor(): array
    {
        return [
            'reservado' => 'Reservado',
            'asignado' => 'Asignado',
            'embarcado' => 'Embarcado',
            'entransito' => 'En tránsito',
            'arribado' => 'Arribado',
            'aduana' => 'Aduana',
            'liberado' => 'Liberado',
            'entregado' => 'Entregado',
        ];
    }
}
