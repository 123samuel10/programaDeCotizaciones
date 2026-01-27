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
    $estado = $request->get('estado'); // filtro para la tabla

    // Base query SOLO con búsqueda (q) - sin estado
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

    // Métricas PRO (basadas en la búsqueda, no en el filtro de estado de la tabla)
    $totalVentas = (clone $baseQuery)->count();

    $ingresosCobrados = (float) (clone $baseQuery)
        ->where('estado_venta', 'pagada')
        ->sum('total_venta');

    $porCobrar = (float) (clone $baseQuery)
        ->where('estado_venta', 'pendiente_pago')
        ->sum('total_venta');

    // Query de la tabla (aquí sí aplicamos estado si el usuario lo eligió)
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
        ]);

        return view('admin.ventas.show', compact('venta'));
    }

    // Cambiar estado: pendiente_pago | pagada | cancelada
    public function update(Request $request, Venta $venta)
    {
        $this->validarAdmin();

        $request->validate([
            'estado_venta' => 'required|in:pendiente_pago,pagada,cancelada',
            'metodo_pago' => 'nullable|string|max:120',
            'notas_internas' => 'nullable|string|max:2000',
        ]);

        $data = [
            'estado_venta' => $request->estado_venta,
            'metodo_pago' => $request->metodo_pago,
            'notas_internas' => $request->notas_internas,
        ];

        if ($request->estado_venta === 'pagada' && !$venta->pagada_en) {
            $data['pagada_en'] = now();
        }

        if ($request->estado_venta !== 'pagada') {
            $data['pagada_en'] = null;
        }

        $venta->update($data);

        return back()->with('success', 'Venta actualizada correctamente.');
    }
}
