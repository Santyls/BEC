<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AlbergueController;
use App\Http\Controllers\Admin\CampanaController;
use App\Http\Controllers\Admin\VoluntariadoController;
use App\Http\Controllers\Admin\DonacionController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\CatalogoController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas de Autenticación
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protegemos el panel de administración
Route::prefix('admin')->name('admin.')->middleware('auth.api')->group(function () {
    
    Route::get('/dashboard', function () { 
        return view('admin.dashboard'); 
    })->name('dashboard');

    // CRUD Usuarios (proxy hacia BEC_API)
    Route::prefix('usuarios')->name('usuarios.')->group(function () {
        Route::get('/',         [UsuarioController::class, 'index'])->name('index');
        Route::post('/',        [UsuarioController::class, 'store'])->name('store');
        Route::put('/{id}',     [UsuarioController::class, 'update'])->name('update');
        Route::patch('/{id}',   [UsuarioController::class, 'patch'])->name('patch');
        Route::delete('/{id}',  [UsuarioController::class, 'destroy'])->name('destroy');
    });

    // Proxy de catálogos hacia BEC_API (sin CORS)
    Route::prefix('catalogos')->name('catalogos.')->group(function () {
        Route::get('/estados',                         [CatalogoController::class, 'estados'])->name('estados');
        Route::get('/estados/{id}/municipios',         [CatalogoController::class, 'municipios'])->name('municipios');
        Route::get('/municipios/{id}/colonias',        [CatalogoController::class, 'colonias'])->name('colonias');
        Route::post('/direcciones',                    [CatalogoController::class, 'storeDireccion'])->name('direcciones.store');
    });

    // CRUD Albergues
    Route::prefix('albergues')->name('albergues.')->group(function () {
        Route::get('/', [AlbergueController::class, 'index'])->name('index');
        Route::get('/crear', function () { return view('admin.albergues.create'); })->name('create');
        Route::post('/crear', [AlbergueController::class, 'store'])->name('store');
    });

    // CRUD Campañas
    Route::prefix('campanas')->name('campanas.')->group(function () {
        Route::get('/', [CampanaController::class, 'index'])->name('index');
        Route::get('/crear', function () { return view('admin.campanas.create'); })->name('create');
        Route::post('/crear', [CampanaController::class, 'store'])->name('store');
    });

    // CRUD Voluntariados
    Route::prefix('voluntariados')->name('voluntariados.')->group(function () {
        Route::get('/', [VoluntariadoController::class, 'index'])->name('index');
        Route::get('/crear', function () { return view('admin.voluntariados.create'); })->name('create');
        Route::post('/crear', [VoluntariadoController::class, 'store'])->name('store');
    });

    // CRUD Donaciones
    Route::prefix('donaciones')->name('donaciones.')->group(function () {
        Route::get('/', [DonacionController::class, 'index'])->name('index');
        Route::get('/crear', function () { return view('admin.donaciones.create'); })->name('create');
        Route::post('/crear', [DonacionController::class, 'store'])->name('store');
    });

    // Reportes
    Route::get('/reportes', function () { return view('admin.reportes.index'); })->name('reportes.index');
});