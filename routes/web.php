<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\MarkerController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


// ------------------------
// RUTAS PÚBLICAS (autenticación web)
// ------------------------
Route::controller(AuthController::class)->group(function () {
    Route::get('login', 'showLoginForm')->name('login');
    Route::post('login', 'login')->name('login.post');
    Route::get('register', 'showRegisterForm')->name('register');
    Route::post('register', 'register')->name('register.post');
    Route::post('logout', 'logout')->name('logout');
});

// ------------------------
// RUTAS PRIVADAS WEB (requieren usuario autenticado)
// ------------------------
Route::middleware('auth')->group(function () {

    // Menú principal
    Route::get('/MenuPrincipal', [PageController::class, 'MenuPrincipal'])->name('MenuPrincipal');

    // Perfil
    Route::get('/perfil', [PageController::class, 'perfil'])->name('perfil');
    Route::post('/perfil', [PageController::class, 'actualizarPerfil'])->name('perfil.actualizar');

    // Entrenos
    Route::get('/entrenos', [MarkerController::class, 'index'])->name('entrenos');

    // Carreras
    Route::get('/carreras', [PageController::class, 'carreras'])->name('carreras');

    // Mapa y marcadores
    Route::get('/mapa', [MarkerController::class, 'index'])->name('mapa');
    Route::post('/marker', [MarkerController::class, 'store'])->name('marker.store');
    Route::get('/marker/{marker}/edit', [MarkerController::class, 'edit'])->name('marker.edit');
    Route::put('/marker/{marker}', [MarkerController::class, 'update'])->name('marker.update');
    Route::resource('marker', MarkerController::class)->except(['create', 'show']);

    // Subidas de archivos
    Route::post('/upload', [UploadController::class, 'upload'])->name('upload');

    // Notificaciones (web)
    Route::post('/send-notification', [NotificationController::class, 'send']);
    Route::get('/send-test', [NotificationController::class, 'sendTest']);

    // SPA catch-all (opcional)
    Route::get('/{any}', [PageController::class, 'MenuPrincipal'])
        ->where('any', '.*')
        ->middleware('auth');
});
