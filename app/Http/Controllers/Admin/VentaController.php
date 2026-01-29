<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Venta;
use Illuminate\Support\Facades\Auth;

class VentaController extends Controller
{
    // Seguridad SIN middleware
    private function validarAdmin()
    {
        $user = Auth::user();

        if (!$user) abort(403, 'Debes iniciar sesión.');
        if (($user->role ?? null) !== 'admin') abort(403, 'Acceso solo para administradores.');

        return $user;
    }

    public function index(Request $request)
    {
        $this->validarAdmin();

        $q = trim((string) $request->get('q', ''));
        $estado = $request->get('estado');

        $baseQuery = Venta::query()
            ->with(['usuario', 'cotizacion.usuario'])
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {

                    if (is_numeric($q)) {
                        $sub->where('id', (int) $q)
                            ->orWhere('cotizacion_id', (int) $q);
                        return;
                    }

                    $sub->whereHas('usuario', function ($u) use ($q) {
                            $u->where('name', 'like', "%{$q}%")
                              ->orWhere('email', 'like', "%{$q}%");
                        })
                        ->orWhereHas('cotizacion.usuario', function ($u) use ($q) {
                            $u->where('name', 'like', "%{$q}%")
                              ->orWhere('email', 'like', "%{$q}%");
                        });
                });
            });

        // Métricas (no dependen del filtro estado de la tabla)
        $totalVentas = (clone $baseQuery)->count();

        $ingresosCobrados = (float) (clone $baseQuery)
            ->where('estado_venta', 'pagada')
            ->sum('total_venta');

        $porCobrar = (float) (clone $baseQuery)
            ->where('estado_venta', 'pendiente_pago')
            ->sum('total_venta');

        $ventasQuery = (clone $baseQuery)
            ->when($estado, fn($query) => $query->where('estado_venta', $estado))
            ->latest();

        $ventas = $ventasQuery->paginate(12)->withQueryString();

        return view('admin.ventas.index', compact(
            'ventas',
            'totalVentas',
            'ingresosCobrados',
            'porCobrar'
        ));
    }

    public function show(Venta $venta)
    {
        $this->validarAdmin();

        $venta->load([
            'usuario',
            'cotizacion',
            'items.opciones',
             'seguimiento', // agrega esto
        ]);

        return view('admin.ventas.show', compact('venta'));
    }

    //  Gestión manual (PRO: no deja “pagada” si hay comprobante sin aprobar)
    public function update(Request $request, Venta $venta)
    {
        $this->validarAdmin();

        $request->validate([
            'estado_venta' => 'required|in:pendiente_pago,pagada,cancelada',
            'metodo_pago' => 'nullable|string|max:120',
            'notas_internas' => 'nullable|string|max:2000',
        ]);

        //  Regla PRO:
        // Si hay comprobante y NO está aceptado, NO permitir pagada manual.
        if (
            $request->estado_venta === 'pagada'
            && !empty($venta->comprobante_path)
            && ($venta->comprobante_estado ?? null) !== 'aceptado'
        ) {
            return back()->with('error', 'Para marcar como PAGADA debes aprobar primero el comprobante.');
        }

        $data = [
            'estado_venta' => $request->estado_venta,
            'metodo_pago' => $request->metodo_pago,
            'notas_internas' => $request->notas_internas,
        ];

        // pagada_en coherente
        if ($request->estado_venta === 'pagada') {
            $data['pagada_en'] = $venta->pagada_en ?: now();
        } else {
            $data['pagada_en'] = null;
        }

        // Si cancela y había comprobante pendiente, lo cerramos para que no quede “pendiente” eterno
        if (
            $request->estado_venta === 'cancelada'
            && !empty($venta->comprobante_path)
            && ($venta->comprobante_estado ?? null) === 'pendiente_revision'
        ) {
            $data['comprobante_estado'] = 'rechazado';
            $data['comprobante_nota_admin'] = $venta->comprobante_nota_admin ?: 'Venta cancelada por el administrador.';
        }

        $venta->update($data);

        return back()->with('success', 'Venta actualizada correctamente.');
    }

    // Aprobar / rechazar comprobante (1 endpoint PRO)
    public function decisionPago(Request $request, Venta $venta)
    {
        $this->validarAdmin();

        $request->validate([
            'decision' => 'required|in:aceptar,rechazar',
            'nota' => 'nullable|string|max:1000',
        ]);

        if (empty($venta->comprobante_path)) {
            return back()->with('error', 'No hay comprobante para revisar.');
        }

        // Solo decidir si está pendiente_revision
        if (($venta->comprobante_estado ?? null) !== 'pendiente_revision') {
            return back()->with('error', 'Este comprobante ya fue revisado.');
        }

        if ($request->decision === 'aceptar') {
            $venta->update([
                'estado_venta' => 'pagada',
                'pagada_en' => now(),
                'comprobante_estado' => 'aceptado',
                'comprobante_nota_admin' => null,
                'metodo_pago' => $venta->metodo_pago ?: 'transferencia',
            ]);

            return back()->with('success', 'Pago aprobado. Venta marcada como PAGADA.');
        }

        // rechazar
        $venta->update([
            'estado_venta' => 'pendiente_pago',
            'pagada_en' => null,
            'comprobante_estado' => 'rechazado',
            'comprobante_nota_admin' => $request->nota,
        ]);

        return back()->with('success', 'Comprobante rechazado.');
    }
}
