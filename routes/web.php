<?php

use App\Http\Controllers\Admin\ClienteController;
use App\Http\Controllers\Admin\CotizacionController;
use App\Http\Controllers\Admin\ProductoController;
use App\Http\Controllers\Admin\ProveedorController;
use App\Http\Controllers\Admin\SeguimientoController;
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
    'proveedores' => 'proveedor',
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


    Route::patch('cotizaciones/{cotizacion}/cancelar', [CotizacionController::class, 'cancelar'])
    ->name('cotizaciones.cancelar');


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








    //seguimientosss
    // Proveedores
Route::resource('proveedores', ProveedorController::class);

// Seguimientos
Route::get('seguimientos', [SeguimientoController::class, 'index'])->name('seguimientos.index');
Route::get('seguimientos/{seguimiento}', [SeguimientoController::class, 'show'])->name('seguimientos.show');
Route::put('seguimientos/{seguimiento}', [SeguimientoController::class, 'update'])->name('seguimientos.update');

// Crear seguimiento desde Venta
Route::get('ventas/{venta}/seguimiento/crear', [SeguimientoController::class, 'createFromVenta'])
    ->name('ventas.seguimiento.create');

Route::post('ventas/{venta}/seguimiento', [SeguimientoController::class, 'storeFromVenta'])
    ->name('ventas.seguimiento.store');

// Contenedores
Route::post('seguimientos/{seguimiento}/contenedores', [SeguimientoController::class, 'contenedorStore'])
    ->name('seguimientos.contenedores.store');

Route::delete('seguimientos/{seguimiento}/contenedores/{contenedor}', [SeguimientoController::class, 'contenedorDestroy'])
    ->name('seguimientos.contenedores.destroy');

// Eventos
Route::post('seguimientos/{seguimiento}/eventos', [SeguimientoController::class, 'eventoStore'])
    ->name('seguimientos.eventos.store');

Route::delete('seguimientos/{seguimiento}/eventos/{evento}', [SeguimientoController::class, 'eventoDestroy'])
    ->name('seguimientos.eventos.destroy');

Route::put(
    'seguimientos/{seguimiento}/contenedores/{contenedor}',
    [SeguimientoController::class, 'contenedorUpdate']
)->name('seguimientos.contenedores.update');


});




Route::prefix('cliente')->name('cliente.')->group(function () {

    Route::get('cotizaciones', [CotizacionClienteController::class, 'index'])
        ->name('cotizaciones.index');
        Route::get('cotizaciones/{cotizacion}/detalle', [CotizacionClienteController::class, 'detalle'])
        ->name('cotizaciones.detalle');

    Route::post('cotizaciones/{cotizacion}/aceptar', [CotizacionClienteController::class, 'aceptar'])
        ->name('cotizaciones.aceptar');

    Route::post('cotizaciones/{cotizacion}/rechazar', [CotizacionClienteController::class, 'rechazar'])
        ->name('cotizaciones.rechazar');

        Route::post('ventas/{venta}/metodo', [PagoVentaController::class, 'guardarMetodo'])
    ->name('ventas.metodo');

Route::post('ventas/{venta}/comprobante', [PagoVentaController::class, 'subirComprobante'])
    ->name('ventas.comprobante');
});


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
