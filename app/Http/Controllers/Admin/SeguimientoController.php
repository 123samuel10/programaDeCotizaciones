<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

use App\Models\Venta;
use App\Models\Seguimiento;
use App\Models\Proveedor;
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

    // ✅ Catálogo Incoterms (pro + explicación)
private function incoterms(): array
{
    return [
        'EXW' => [
            'label' => 'Ex Works (En fábrica)',
            'desc'  => 'El vendedor entrega en su bodega/fábrica. El comprador asume transporte, exportación, flete, seguro e importación.',
            'permite_contenedores' => false,
        ],
        'FOB' => [
            'label' => 'Free On Board (A bordo)',
            'desc'  => 'El vendedor entrega la carga en el puerto de salida (a bordo). Desde ahí, el riesgo/costo principal es del comprador.',
            'permite_contenedores' => true, // ✅ SÍ permite
        ],
        'CIF' => [
            'label' => 'Cost, Insurance & Freight',
            'desc'  => 'El vendedor paga costo + flete + seguro hasta puerto destino. Aduana e inland suelen ser del comprador.',
            'permite_contenedores' => true, // ✅ SÍ permite
        ],
    ];
}


    public function index(Request $request)
    {
        $this->validarAdmin();

        $q = trim((string) $request->get('q', ''));
        $estado = $request->get('estado');
        $tipo_envio = $request->get('tipo_envio');
        $incoterm = $request->get('incoterm');
        $eta_vencida = $request->boolean('eta_vencida', false);

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
            ->when($tipo_envio, fn($query) => $query->where('tipo_envio', $tipo_envio))
            ->when($incoterm, fn($query) => $query->where('incoterm', $incoterm))
            ->when($eta_vencida, function($query){
                $query->whereNotNull('eta')->whereDate('eta', '<', now()->startOfDay());
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $estados = $this->estados();
        $incoterms = $this->incoterms();

        return view('admin.seguimientos.index', compact(
            'seguimientos','q','estado','estados','tipo_envio','incoterm','eta_vencida','incoterms'
        ));
    }

    public function createFromVenta(Venta $venta)
    {
        $this->validarAdmin();
        $venta->load(['usuario', 'seguimiento']);

        if ($venta->seguimiento) {
            return redirect()->route('admin.seguimientos.show', $venta->seguimiento->id);
        }

        $proveedores = Proveedor::orderBy('nombre')->get();
        $estados = $this->estados();
        $incoterms = $this->incoterms();

        return view('admin.seguimientos.create', compact('venta', 'proveedores', 'estados', 'incoterms'));
    }

    public function storeFromVenta(Request $request, Venta $venta)
    {
        $this->validarAdmin();

        if ($venta->seguimiento()->exists()) {
            return redirect()->route('admin.seguimientos.show', $venta->seguimiento->id)
                ->with('success', 'Este seguimiento ya existía.');
        }

        // Validación base
        $data = $request->validate([
            'proveedor_id' => 'nullable|exists:proveedores,id',
            'pais_destino' => 'nullable|string|max:120',
            'tipo_envio' => 'required|in:maritimo,aereo',
            'incoterm' => 'nullable|string|max:50',
            'estado' => 'required|string|max:50',
            'etd' => 'nullable|date',
            'eta' => 'nullable|date|after_or_equal:etd',
            'observaciones' => 'nullable|string|max:5000',

            // AÉREO
            'awb' => 'nullable|string|max:60',
            'aerolinea' => 'nullable|string|max:120',
            'aeropuerto_salida' => 'nullable|string|max:120',
            'aeropuerto_llegada' => 'nullable|string|max:120',
            'vuelo' => 'nullable|string|max:60',
            'tracking_url' => 'nullable|string|max:500',
        ]);

        // ✅ Incoterm detalles (json)
        $incotermDetalles = $request->input('incoterm_detalles', []);
        if (!is_array($incotermDetalles)) $incotermDetalles = [];
        $data['incoterm_detalles'] = $incotermDetalles;

        // ✅ Reglas condicionales por Incoterm (para que sea “ERP real”)
        if (($data['incoterm'] ?? null) === 'EXW') {
            if (empty($incotermDetalles['lugar_retiro'] ?? null)) {
                return back()->withInput()->withErrors([
                    'incoterm_detalles.lugar_retiro' => 'En EXW es obligatorio indicar el lugar de retiro.',
                ]);
            }
        }
        if (($data['incoterm'] ?? null) === 'FOB') {
            if (empty($incotermDetalles['puerto_carga'] ?? null)) {
                return back()->withInput()->withErrors([
                    'incoterm_detalles.puerto_carga' => 'En FOB es obligatorio indicar el puerto de carga.',
                ]);
            }
        }
        if (($data['incoterm'] ?? null) === 'CIF') {
            if (empty($incotermDetalles['puerto_destino'] ?? null)) {
                return back()->withInput()->withErrors([
                    'incoterm_detalles.puerto_destino' => 'En CIF es obligatorio indicar el puerto de destino.',
                ]);
            }
        }

        // ✅ Si es marítimo, limpia campos aéreos; si es aéreo, se permiten
        if (($data['tipo_envio'] ?? null) === 'maritimo') {
            $data['awb'] = null;
            $data['aerolinea'] = null;
            $data['aeropuerto_salida'] = null;
            $data['aeropuerto_llegada'] = null;
            $data['vuelo'] = null;
            $data['tracking_url'] = null;
        }

        $data['venta_id'] = $venta->id;

        $seguimiento = Seguimiento::create($data);

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
    $incoterms = $this->incoterms();

    // ✅ ESTA LÍNEA FALTA
    $permiteContenedores = $this->permiteContenedoresPara($seguimiento);

    return view('admin.seguimientos.show', compact(
        'seguimiento','proveedores','estados','estadosContenedor','incoterms','permiteContenedores'
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
            'eta' => 'nullable|date|after_or_equal:etd',
            'observaciones' => 'nullable|string|max:5000',

            // AÉREO
            'awb' => 'nullable|string|max:60',
            'aerolinea' => 'nullable|string|max:120',
            'aeropuerto_salida' => 'nullable|string|max:120',
            'aeropuerto_llegada' => 'nullable|string|max:120',
            'vuelo' => 'nullable|string|max:60',
            'tracking_url' => 'nullable|string|max:500',
        ]);

        $incotermDetalles = $request->input('incoterm_detalles', []);
        if (!is_array($incotermDetalles)) $incotermDetalles = [];
        $data['incoterm_detalles'] = $incotermDetalles;

        // Condicional incoterm
        if (($data['incoterm'] ?? null) === 'EXW' && empty($incotermDetalles['lugar_retiro'] ?? null)) {
            return back()->withInput()->withErrors([
                'incoterm_detalles.lugar_retiro' => 'En EXW es obligatorio indicar el lugar de retiro.',
            ]);
        }
        if (($data['incoterm'] ?? null) === 'FOB' && empty($incotermDetalles['puerto_carga'] ?? null)) {
            return back()->withInput()->withErrors([
                'incoterm_detalles.puerto_carga' => 'En FOB es obligatorio indicar el puerto de carga.',
            ]);
        }
        if (($data['incoterm'] ?? null) === 'CIF' && empty($incotermDetalles['puerto_destino'] ?? null)) {
            return back()->withInput()->withErrors([
                'incoterm_detalles.puerto_destino' => 'En CIF es obligatorio indicar el puerto de destino.',
            ]);
        }

        if (($data['tipo_envio'] ?? null) === 'maritimo') {
            $data['awb'] = null;
            $data['aerolinea'] = null;
            $data['aeropuerto_salida'] = null;
            $data['aeropuerto_llegada'] = null;
            $data['vuelo'] = null;
            $data['tracking_url'] = null;
        }

        $cambioEstado = $seguimiento->estado !== $data['estado'];
        $seguimiento->update($data);

        if ($cambioEstado) {
            SeguimientoEvento::create([
                'seguimiento_id' => $seguimiento->id,
                'creado_por' => Auth::id(),
                'tipo' => 'general',
                'titulo' => 'Cambio de estado',
                'descripcion' => 'Nuevo estado: ' . ($this->estados()[$data['estado']] ?? $data['estado']),
                'fecha_evento' => now(),
            ]);
        }

        return back()->with('success', 'Seguimiento actualizado.');
    }

    // -------- CONTENEDORES --------
    public function contenedorStore(Request $request, Seguimiento $seguimiento)
    {
        $this->validarAdmin();

        if (($seguimiento->tipo_envio ?? null) !== 'maritimo') {
            return back()->with('error', 'Este seguimiento es AÉREO. No aplica contenedores.');
        }
if (!$this->permiteContenedoresPara($seguimiento)) {
        $inc = $seguimiento->incoterm ?? '—';
        return back()->with('error', "Para Incoterm {$inc} no aplica gestión de contenedores en este sistema.");
    }
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

    public function contenedorUpdate(Request $request, Seguimiento $seguimiento, Contenedor $contenedor)
    {
        $this->validarAdmin();
        if ($contenedor->seguimiento_id !== $seguimiento->id) abort(403);

        if (($seguimiento->tipo_envio ?? null) !== 'maritimo') {
            return back()->with('error', 'Este seguimiento es AÉREO. No aplica contenedores.');
        }
          if (!$this->permiteContenedoresPara($seguimiento)) {
        $inc = $seguimiento->incoterm ?? '—';
        return back()->with('error', "Para Incoterm {$inc} no aplica gestión de contenedores en este sistema.");
    }

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

        $cambioEstado = ($contenedor->estado ?? null) !== $data['estado'];
        $contenedor->update($data);

        if ($cambioEstado) {
            $label = $this->estadosContenedor()[$data['estado']] ?? $data['estado'];

            SeguimientoEvento::create([
                'seguimiento_id' => $seguimiento->id,
                'creado_por' => Auth::id(),
                'tipo' => 'embarque',
                'titulo' => 'Estado de contenedor actualizado',
                'descripcion' => 'Contenedor ' . ($contenedor->numero_contenedor ?? ('#'.$contenedor->id)) . ' → ' . $label,
                'fecha_evento' => now(),
            ]);
        }

        return back()->with('success', 'Contenedor actualizado.');
    }

  public function contenedorDestroy(Seguimiento $seguimiento, Contenedor $contenedor)
{
    $this->validarAdmin();
    if ($contenedor->seguimiento_id !== $seguimiento->id) abort(403);

    if (($seguimiento->tipo_envio ?? null) !== 'maritimo') {
        return back()->with('error', 'Este seguimiento es AÉREO. No aplica contenedores.');
    }

    if (!$this->permiteContenedoresPara($seguimiento)) {
        $inc = $seguimiento->incoterm ?? '—';
        return back()->with('error', "Para Incoterm {$inc} no aplica gestión de contenedores en este sistema.");
    }

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
            'ordencompra'       => 'Compra confirmada',
            'produccion'        => 'En fabricación',
            'listoparaembarque' => 'Listo para despachar',
            'embarcado'         => 'Despachado / Embarcado',
            'entransito'        => 'En camino (Tránsito)',
            'arriboapuerto'     => 'Llegó al puerto',
            'aduana'            => 'En revisión de aduana',
            'liberado'          => 'Liberado (listo para entrega)',
            'entregado'         => 'Entregado al cliente',
            'cerrado'           => 'Caso cerrado',
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

//     private function permiteContenedoresPara(Seguimiento $seguimiento): bool
// {
//     if (($seguimiento->tipo_envio ?? null) !== 'maritimo') return false;

//     $incoterm = $seguimiento->incoterm ?? null;
//     if (!$incoterm) return false; // si no hay incoterm, por seguridad no mostramos

//     $incoterms = $this->incoterms();
//     return (bool) ($incoterms[$incoterm]['permite_contenedores'] ?? false);
// }
private function permiteContenedoresPara(Seguimiento $seguimiento): bool
{
    if (($seguimiento->tipo_envio ?? null) !== 'maritimo') return false;

    $incoterm = strtoupper(trim((string)($seguimiento->incoterm ?? '')));
    if ($incoterm === '') return false;

    $incoterms = $this->incoterms();
    return (bool) ($incoterms[$incoterm]['permite_contenedores'] ?? false);
}


}
