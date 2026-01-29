<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Venta;

use Illuminate\Support\Facades\Auth;


class PagoVentaController extends Controller
{
 private function validarCliente()
    {
        $u = Auth::user();
        if (!$u) abort(403, 'Debes iniciar sesión.');
        if (($u->role ?? null) !== 'cliente') abort(403, 'Acceso solo para clientes.');
        return $u;
    }

    private function validarPropiedadVenta(Venta $venta, $user)
    {
        if ((int)$venta->user_id !== (int)$user->id) abort(403, 'Esta venta no te pertenece.');
    }

    public function guardarMetodo(Request $request, Venta $venta)
    {
        $user = $this->validarCliente();
        $this->validarPropiedadVenta($venta, $user);

        $request->validate([
            'metodo_pago' => 'required|in:efectivo,transferencia',
        ]);

        $venta->update([
            'metodo_pago' => $request->metodo_pago,
        ]);

        // ✅ Volver a la lista ABRIENDO el modal correcto y anclando al pago
        $prev = url()->previous();
        $sep  = str_contains($prev, '?') ? '&' : '?';

        return redirect()
            ->to($prev . $sep . 'open=' . $venta->cotizacion_id . '#pago-' . $venta->id)
            ->with('pago_step', $request->metodo_pago)          // efectivo | transferencia
            ->with('open_detalle', $venta->cotizacion_id);      // para asegurar apertura del modal
    }

    public function subirComprobante(Request $request, Venta $venta)
    {
        $user = $this->validarCliente();
        $this->validarPropiedadVenta($venta, $user);

        if ($venta->metodo_pago !== 'transferencia') {
            return back()->with('error', 'Selecciona “transferencia” primero.');
        }

        $request->validate([
            'comprobante' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'referencia_pago' => 'nullable|string|max:120',
        ]);

        $path = $request->file('comprobante')->store('comprobantes', 'public');

        $venta->update([
            'referencia_pago' => $request->referencia_pago,
            'comprobante_path' => $path,
            'comprobante_subido_en' => now(),
            'comprobante_estado' => 'pendiente_revision',
        ]);

        // ✅ Mismo truco: mantener al cliente dentro del modal en el pago
        $prev = url()->previous();
        $sep  = str_contains($prev, '?') ? '&' : '?';

        return redirect()
            ->to($prev . $sep . 'open=' . $venta->cotizacion_id . '#pago-' . $venta->id)
            ->with('open_detalle', $venta->cotizacion_id)
            ->with('success', 'Comprobante enviado. Queda pendiente de revisión.');
    }
}
