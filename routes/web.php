<?php

use App\Http\Controllers\Admin\ClienteController;
use App\Http\Controllers\Admin\CotizacionController;
use App\Http\Controllers\Admin\ProductoController;
use App\Http\Controllers\Admin\VentaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cliente\CotizacionClienteController;
use App\Http\Controllers\Cliente\PagoVentaController;
use App\Http\Controllers\PublicCotizacionController;

Route::get('/', function () {
    return view('welcome');
});




Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::resourceParameters([
    'cotizaciones' => 'cotizacion',
    'productos' => 'producto',
]);

Route::prefix('admin')->as('admin.')->group(function () {

    // Recursos principales
    Route::resource('productos', ProductoController::class);
      // ESTA ES LA QUE TE FALTA
    Route::post('productos/{producto}/opciones', [ProductoController::class, 'agregarOpcion'])
        ->name('productos.opciones.store');

    Route::resource('cotizaciones', CotizacionController::class);


    // ITEMS (líneas de cotización)
    Route::post('cotizaciones/{cotizacion}/items', [CotizacionController::class, 'agregarItem'])
        ->name('cotizaciones.items.store');

    Route::patch('cotizaciones/{cotizacion}/items/{item}', [CotizacionController::class, 'actualizarItem'])
        ->name('cotizaciones.items.update');

    Route::delete('cotizaciones/{cotizacion}/items/{item}', [CotizacionController::class, 'eliminarItem'])
        ->name('cotizaciones.items.destroy');

    // ADICIONES POR ITEM (no por cotización)
    Route::post('cotizaciones/{cotizacion}/items/{item}/opciones', [CotizacionController::class, 'agregarOpcionItem'])
        ->name('cotizaciones.items.opciones.store');

    Route::delete('cotizaciones/{cotizacion}/items/{item}/opciones/{op}', [CotizacionController::class, 'eliminarOpcionItem'])
        ->name('cotizaciones.items.opciones.destroy');

        Route::resource('clientes', ClienteController::class);



          Route::get('ventas', [VentaController::class, 'index'])->name('ventas.index');

Route::get('ventas/{venta}', [VentaController::class, 'show'])->name('ventas.show');
Route::put('ventas/{venta}', [VentaController::class, 'update'])->name('ventas.update');

Route::post('ventas/{venta}/decision', [VentaController::class, 'decisionPago'])
    ->name('ventas.decision');
    Route::put('ventas/{venta}/decision', [VentaController::class, 'decisionPago'])
    ->name('ventas.decision');


    Route::post('cotizaciones/{cotizacion}/enviar-correo', [CotizacionController::class, 'enviarPorCorreo'])
    ->name('cotizaciones.enviarCorreo');

});




Route::prefix('cliente')->name('cliente.')->group(function () {

    Route::get('cotizaciones', [CotizacionClienteController::class, 'index'])
        ->name('cotizaciones.index');

    Route::post('cotizaciones/{cotizacion}/aceptar', [CotizacionClienteController::class, 'aceptar'])
        ->name('cotizaciones.aceptar');

    Route::post('cotizaciones/{cotizacion}/rechazar', [CotizacionClienteController::class, 'rechazar'])
        ->name('cotizaciones.rechazar');

        Route::post('ventas/{venta}/metodo', [PagoVentaController::class, 'guardarMetodo'])
    ->name('ventas.metodo');

Route::post('ventas/{venta}/comprobante', [PagoVentaController::class, 'subirComprobante'])
    ->name('ventas.comprobante');
});


// Route::prefix('c')->name('public.cotizacion.')->group(function () {

//     // Ver cotización (página pública)
//     Route::get('{token}', [PublicCotizacionController::class, 'ver'])
//         ->name('ver');

//     // Confirmaciones (pantalla antes de ejecutar)
//     Route::get('{token}/confirmar-aceptar', [PublicCotizacionController::class, 'confirmarAceptar'])
//         ->name('confirmar.aceptar');

//     Route::get('{token}/confirmar-rechazar', [PublicCotizacionController::class, 'confirmarRechazar'])
//         ->name('confirmar.rechazar');

//     // Ejecutar acciones (POST, porque cambian estado y crean venta)
//     Route::post('{token}/aceptar', [PublicCotizacionController::class, 'aceptar'])
//         ->name('aceptar.post');

//     Route::post('{token}/rechazar', [PublicCotizacionController::class, 'rechazar'])
//         ->name('rechazar.post');

//     // PDF descarga
//     Route::get('{token}/pdf', [PublicCotizacionController::class, 'pdf'])
//         ->name('pdf');
// });

// Route::get('/cotizacion/{token}', [PublicCotizacionController::class, 'ver'])
//     ->name('public.cotizacion.ver');

// // Confirmación (pantalla)
// Route::get('/cotizacion/{token}/aceptar', [PublicCotizacionController::class, 'confirmarAceptar'])
//     ->name('public.cotizacion.aceptar');

// Route::get('/cotizacion/{token}/rechazar', [PublicCotizacionController::class, 'confirmarRechazar'])
//     ->name('public.cotizacion.rechazar');

// // Acción real (POST)
// Route::post('/cotizacion/{token}/aceptar', [PublicCotizacionController::class, 'aceptar'])
//     ->name('public.cotizacion.aceptar.post');

// Route::post('/cotizacion/{token}/rechazar', [PublicCotizacionController::class, 'rechazar'])
//     ->name('public.cotizacion.rechazar.post');
// Route::get('/cotizacion/{token}/pdf', [PublicCotizacionController::class, 'pdf'])
//     ->name('public.cotizacion.pdf');

Route::prefix('cotizacion')->name('public.cotizacion.')->group(function () {

    // Ver cotización
    Route::get('{token}', [PublicCotizacionController::class, 'ver'])
        ->name('ver');

    // Confirmación (pantalla)
    Route::get('{token}/aceptar', [PublicCotizacionController::class, 'confirmarAceptar'])
        ->name('confirmar.aceptar');

    Route::get('{token}/rechazar', [PublicCotizacionController::class, 'confirmarRechazar'])
        ->name('confirmar.rechazar');

    // Acción real (POST)
    Route::post('{token}/aceptar', [PublicCotizacionController::class, 'aceptar'])
        ->name('aceptar.post');

    Route::post('{token}/rechazar', [PublicCotizacionController::class, 'rechazar'])
        ->name('rechazar.post');

    // PDF
    Route::get('{token}/pdf', [PublicCotizacionController::class, 'pdf'])
        ->name('pdf');
});

require __DIR__.'/auth.php';
