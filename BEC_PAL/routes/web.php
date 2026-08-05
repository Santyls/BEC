<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'mostrarFormulario'])->name('login');
Route::post('/login', [LoginController::class, 'iniciarSesion'])->name('login.attempt');
Route::post('/logout', [LoginController::class, 'cerrarSesion'])->name('logout');

// Agrupamos todas las rutas del administrador
Route::prefix('admin')->name('admin.')->middleware('bec.auth')->group(function () {

    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Mi Perfil (datos de solo lectura + teléfono, contraseña y foto editables)
    Route::prefix('perfil')->name('perfil.')->controller(\App\Http\Controllers\Admin\PerfilController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::put('/telefono', 'actualizarTelefono')->name('telefono');
        Route::put('/password', 'actualizarPassword')->name('password');
        Route::post('/foto', 'actualizarFoto')->name('foto');
    });

    // CRUD Usuarios (Incluye Recepcionistas por Rol)
    Route::prefix('usuarios')->name('usuarios.')->controller(\App\Http\Controllers\Admin\UsuarioController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/crear', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{id}/editar', 'edit')->name('edit');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
        Route::post('/{id}/reactivar', 'reactivar')->name('reactivar');
        Route::delete('/{id}/permanente', 'eliminarPermanente')->name('eliminarPermanente');
        Route::put('/{id}/vetar', 'vetar')->name('vetar');
    });

    // CRUD Albergues
    Route::prefix('albergues')->name('albergues.')->controller(\App\Http\Controllers\Admin\AlbergueController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/crear', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{id}/editar', 'edit')->name('edit');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
        Route::post('/{id}/reactivar', 'reactivar')->name('reactivar');
        Route::delete('/{id}/permanente', 'eliminarPermanente')->name('eliminarPermanente');
    });

    // CRUD Campañas
    Route::prefix('campanas')->name('campanas.')->controller(\App\Http\Controllers\Admin\CampanaController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/crear', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{id}/editar', 'edit')->name('edit');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });

    // CRUD Voluntariados
    Route::prefix('voluntariados')->name('voluntariados.')->controller(\App\Http\Controllers\Admin\VoluntariadoController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/crear', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{id}', 'show')->name('show');
        Route::get('/{id}/editar', 'edit')->name('edit');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
        Route::put('/{id}/inscripciones/{inscripcionId}/cancelar', 'cancelarInscripcion')->name('inscripciones.cancelar');
    });

    // Donaciones (registro histórico: sin editar/eliminar, igual que en la API)
    Route::prefix('donaciones')->name('donaciones.')->controller(\App\Http\Controllers\Admin\DonacionController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/crear', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{id}/editar', 'edit')->name('edit');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });

    // Generación de Reportes
    Route::prefix('reportes')->name('reportes.')->controller(\App\Http\Controllers\Admin\ReporteController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/donaciones/exportar-xlsx', 'exportarDonacionesXlsx')->name('donaciones.xlsx');
        Route::get('/donaciones/exportar-pdf', 'exportarDonacionesPdf')->name('donaciones.pdf');
        Route::get('/voluntariados/exportar-xlsx', 'exportarVoluntariadosXlsx')->name('voluntariados.xlsx');
        Route::get('/voluntariados/exportar-pdf', 'exportarVoluntariadosPdf')->name('voluntariados.pdf');
    });

});