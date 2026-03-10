<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Agrupamos todas las rutas del administrador
Route::prefix('admin')->name('admin.')->group(function () {
    
    Route::get('/dashboard', function () { 
        return view('admin.dashboard'); 
    })->name('dashboard');

    // CRUD Usuarios (Incluye Recepcionistas por Rol)
    Route::get('/usuarios', function () { 
        return view('admin.usuarios.index'); 
    })->name('usuarios.index');

    // CRUD Albergues
    Route::prefix('albergues')->name('albergues.')->group(function () {
        Route::get('/', function () { return view('admin.albergues.index'); })->name('index');
        Route::get('/crear', function () { return view('admin.albergues.create'); })->name('create');
    });

    // CRUD Campañas
    Route::prefix('campanas')->name('campanas.')->group(function () {
        Route::get('/', function () { return view('admin.campanas.index'); })->name('index');
        Route::get('/crear', function () { return view('admin.campanas.create'); })->name('create');
    });

    // CRUD Voluntariados
    Route::prefix('voluntariados')->name('voluntariados.')->group(function () {
        Route::get('/', function () { return view('admin.voluntariados.index'); })->name('index');
        Route::get('/crear', function () { return view('admin.voluntariados.create'); })->name('create');
    });

    // CRUD Donaciones
    Route::prefix('donaciones')->name('donaciones.')->group(function () {
        Route::get('/', function () { return view('admin.donaciones.index'); })->name('index');
        Route::get('/crear', function () { return view('admin.donaciones.create'); })->name('create');
    });

    // Generación de Reportes
    Route::get('/reportes', function () { 
        return view('admin.reportes.index'); 
    })->name('reportes.index');

});