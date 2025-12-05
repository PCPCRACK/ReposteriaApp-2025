<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Admin\CompraController;
use App\Http\Controllers\Admin\ClienteController;
use App\Http\Controllers\Admin\EmpleadoController;
use App\Http\Controllers\Admin\CajeroController; // Add this line
use App\Http\Controllers\Admin\ReposteroController; // Add this line
use App\Http\Controllers\Admin\DomiciliarioController; // Add this line
use App\Http\Controllers\Admin\ProductoController; // Add this line
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PagoController;
use App\Http\Controllers\Admin\RecetaController;

use App\Http\Controllers\Admin\PedidoController;
use App\Http\Controllers\Cajero\DashboardController as CajeroDashboardController;
use App\Http\Controllers\Repostero\DashboardController as ReposteroDashboardController;

Route::get('/login', function() {

    return view('auth.login');

})->name('login.form');



Route::post('/login', [LoginController::class, 'authenticate'])->name('login');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');



Route::get('/', function() {

    return view('auth.login');

});



// Admin Routes

Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
    Route::get('/pedidos/create', [PedidoController::class, 'create'])->name('pedidos.create');
    Route::post('/pedidos', [PedidoController::class, 'store'])->name('pedidos.store');
    Route::get('/pedidos/{pedido}', [PedidoController::class, 'show'])->name('pedidos.show');


    Route::resource('clientes', ClienteController::class)->except(['show']);

    Route::resource('proveedores', App\Http\Controllers\Admin\ProveedorController::class)->except(['show']);

    Route::get('empleados', [EmpleadoController::class, 'index'])->name('empleados.index');

    Route::resource('cajeros', CajeroController::class)->except(['index', 'show']);

    Route::resource('reposteros', ReposteroController::class)->except(['index', 'show']);

    Route::resource('domiciliarios', DomiciliarioController::class)->except(['index', 'show']);

    Route::resource('productos', ProductoController::class)->except(['create', 'store', 'show']);

    Route::get('productos/{producto}/presentaciones', [ProductoController::class, 'showPresentaciones'])->name('productos.showPresentaciones');

    Route::get('productos/{producto}/presentaciones/create', [ProductoController::class, 'createPresentacion'])->name('productos.createPresentacion');

    Route::post('productos/{producto}/presentaciones', [ProductoController::class, 'storePresentacion'])->name('productos.storePresentacion');

    Route::delete('presentaciones/{presentacion}', [ProductoController::class, 'destroyPresentacion'])->name('presentaciones.destroy');

    Route::resource('recetas', RecetaController::class)->only(['index']);
    Route::resource('compras', CompraController::class)->only(['index']);
    Route::resource('pagos', PagoController::class)->only(['index']);

});



// Cajero Routes

Route::middleware(['role:cajero'])->prefix('cajero')->name('cajero.')->group(function () {

    Route::get('/dashboard', [CajeroDashboardController::class, 'index'])->name('dashboard');

});



// Repostero Routes

Route::middleware(['role:repostero'])->prefix('repostero')->name('repostero.')->group(function () {

    Route::get('/dashboard', [ReposteroDashboardController::class, 'index'])->name('dashboard');

});

