<?php

use App\Http\Controllers\Admin\CotizacionController;
use App\Http\Controllers\Admin\ProductoController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});






// Route::prefix('admin')->name('admin.')->group(function () {
//     Route::resource('productos', ProductoController::class);

//     Route::post('productos/{producto}/opciones', [ProductoController::class, 'agregarOpcion'])
//         ->name('productos.opciones.store');
// });
Route::resourceParameters([
    'cotizaciones' => 'cotizacion',
    'productos' => 'producto',
]);

Route::prefix('admin')->as('admin.')->group(function () {

    Route::resource('productos', ProductoController::class);

    Route::resource('cotizaciones', CotizacionController::class)
        ->only(['index', 'create', 'store', 'edit']);

    Route::post('cotizaciones/{cotizacion}/opciones', [CotizacionController::class, 'agregarOpcion'])
        ->name('cotizaciones.opciones.store');

    Route::delete('cotizaciones/{cotizacion}/items/{item}', [CotizacionController::class, 'eliminarItem'])
        ->name('cotizaciones.items.destroy');
        Route::post('productos/{producto}/opciones', [ProductoController::class, 'agregarOpcion'])
    ->name('productos.opciones.store');

});


require __DIR__.'/auth.php';
