<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\Models\Venta;

use Illuminate\Support\Facades\Auth;
class VentaController extends Controller
{
  // 🔒 Seguridad SIN middleware
    private function validarAdmin()
    {
        $user = Auth::user();

        if (!$user) abort(403, 'Debes iniciar sesión.');
        if (($user->role ?? null) !== 'admin') abort(403, 'Acceso solo para administradores.');

        return $user;
    }

 public function index()
    {
        $ventas = Venta::with(['cotizacion.usuario'])
            ->latest()
            ->get();

        $totalVentas = $ventas->count();
        $totalIngresos = (float) $ventas->sum('total_venta'); // solo venta, NO costo

        return view('admin.ventas.index', compact('ventas', 'totalVentas', 'totalIngresos'));
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
