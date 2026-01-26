<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Cotizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CotizacionClienteController extends Controller
{
    // 🔒 Seguridad SIN middleware
    private function validarCliente()
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Debes iniciar sesión.');
        }

        if ($user->role !== 'cliente') {
            abort(403, 'Acceso solo para clientes.');
        }

        return $user;
    }

    private function validarPropiedad(Cotizacion $cotizacion, $user)
    {
        if ((int)$cotizacion->user_id !== (int)$user->id) {
            abort(403, 'Esta cotización no te pertenece.');
        }
    }

    // 📋 Listado / notificaciones
    public function index()
    {
        $user = $this->validarCliente();

        $cotizaciones = Cotizacion::with(['items.producto', 'items.opciones.opcion'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $pendientes = $cotizaciones->where('estado', 'pendiente')->count();

        return view('cliente.cotizaciones.index', compact('cotizaciones', 'pendientes'));
    }

    // ✅ Aceptar
    public function aceptar(Request $request, Cotizacion $cotizacion)
    {
        $user = $this->validarCliente();
        $this->validarPropiedad($cotizacion, $user);

        if ($cotizacion->estado !== 'pendiente') {
            return back()->with('error', 'Esta cotización ya fue respondida.');
        }

        $request->validate([
            'nota_cliente' => 'nullable|string|max:1000',
        ]);

        $cotizacion->update([
            'estado' => 'aceptada',
            'respondida_en' => now(),
            'nota_cliente' => $request->nota_cliente,
        ]);

        return back()->with('success', 'Cotización aceptada correctamente.');
    }

    // ❌ Rechazar
    public function rechazar(Request $request, Cotizacion $cotizacion)
    {
        $user = $this->validarCliente();
        $this->validarPropiedad($cotizacion, $user);

        if ($cotizacion->estado !== 'pendiente') {
            return back()->with('error', 'Esta cotización ya fue respondida.');
        }

        $request->validate([
            'nota_cliente' => 'nullable|string|max:1000',
        ]);

        $cotizacion->update([
            'estado' => 'rechazada',
            'respondida_en' => now(),
            'nota_cliente' => $request->nota_cliente,
        ]);

        return back()->with('success', 'Cotización rechazada correctamente.');
    }
}
