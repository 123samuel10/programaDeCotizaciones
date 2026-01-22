<?php

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
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('productos', \App\Http\Controllers\Admin\ProductoController::class);

    Route::post('productos/{producto}/opciones', [\App\Http\Controllers\Admin\ProductoController::class, 'agregarOpcion'])
        ->name('productos.opciones.store');

    Route::post('productos/{producto}/predeterminadas', [\App\Http\Controllers\Admin\ProductoController::class, 'agregarPredeterminadas'])
        ->name('productos.predeterminadas');
});



require __DIR__.'/auth.php';
