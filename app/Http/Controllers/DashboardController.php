<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Producto;
use App\Models\User;
use App\Models\Cotizacion;
use Illuminate\Support\Facades\Auth;


class DashboardController extends Controller
{
   public function index()
    {
     $user = Auth::user();


        if ($user->role === 'admin') {
    $totalProductos = Producto::count();
    $totalClientes = User::where('role', 'cliente')->count();
    $totalCotizaciones = Cotizacion::count();

    return view('dashboard', compact(
        'totalProductos',
        'totalClientes',
        'totalCotizaciones'
    ));
}


        // ✅ CLIENTE: últimas cotizaciones del usuario
        $misCotizaciones = Cotizacion::with(['producto'])
            ->where('user_id', $user->id) // 👈 si tu FK es diferente, me dices y lo ajusto
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard', compact('misCotizaciones'));
    }
}
