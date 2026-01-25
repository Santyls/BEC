<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\ProfileController; 
use App\Http\Controllers\AddressController; 
use App\Http\Controllers\AdminController; 


// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

//AUTENTICACIÓN 
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'processLogin'])->name('login.process');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'processRegister'])->name('register.process');
});

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

//RUTAS PROTEGIDAS (AUTH) ---
Route::middleware('auth')->group(function () {
    
    // Verificación de Email
    Route::get('/email/verify', [VerificationController::class, 'notice'])
        ->name('verification.notice');
        
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
        
    Route::post('/email/verification-notification', [VerificationController::class, 'send'])
        ->middleware(['throttle:6,1'])
        ->name('verification.send');

    // Perfil de Usuario 
    Route::get('/perfil/completar', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/perfil/actualizar', [ProfileController::class, 'update'])->name('profile.update');

    // Dirección 
    Route::get('/perfil/direccion', [AddressController::class, 'create'])->name('address.create');
    Route::post('/perfil/direccion', [AddressController::class, 'store'])->name('address.store');
});

//RUTAS DE ADMINISTRADOR 
Route::prefix('admin')->group(function () {
    // Login de Admin
    Route::get('/login', [App\Http\Controllers\AdminController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [App\Http\Controllers\AdminController::class, 'login'])->name('admin.login.submit'); // Mock

    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Donaciones
    Route::get('/donaciones', [App\Http\Controllers\AdminController::class, 'donations'])->name('admin.donations');
    Route::get('/voluntariados', [App\Http\Controllers\AdminController::class, 'volunteering'])->name('admin.volunteering');
});