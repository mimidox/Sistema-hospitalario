<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ControlInicio;
use App\Http\Controllers\Api\ControlAdmin;
use App\Http\Controllers\Api\ControlDoctor;
use App\Http\Controllers\Api\ControlPaciente;

Route::get('/', function () {
    return view('welcome');
});

// INICIO PAGE
Route::get('/medilab', [ControlInicio::class, 'index'])->name('ControlInicio.index'); 
Route::get('/main', [ControlInicio::class, 'main'])->name('ControlInicio.main'); 
Route::get('/login', [ControlInicio::class, 'login'])->name('ControlInicio.login'); 

// RUTAS DE DOCTORES - ORDEN CORREGIDO
// 1. Rutas que NO tienen parámetros (van primero)
Route::get('/doctores', [ControlDoctor::class, 'index'])->name('doctores.index');
Route::get('/doctores/crear', [ControlDoctor::class, 'create'])->name('doctores.create');
Route::post('/doctores', [ControlDoctor::class, 'store'])->name('doctores.store');

// ✅ RUTA AGREGADA PARA VALIDACIÓN DE LICENCIA
Route::post('/doctores/validar-licencia', [ControlDoctor::class, 'validarLicencia'])
     ->name('doctores.validarLicencia');

Route::get('/doctores/estadisticas', [ControlDoctor::class, 'estadisticas'])->name('doctores.estadisticas');
Route::get('/doctores/reporte/especialidades', [ControlDoctor::class, 'reporteEspecialidades'])->name('doctores.reporte.especialidades');

// 2. Ruta para datos reales (solo desarrollo) - debe ir ANTES de las rutas con {id}
if (env('APP_DEBUG')) {
    Route::get('/doctores/{id}/datos-reales', [ControlDoctor::class, 'datosReales'])
         ->name('doctores.datos.reales');
}

// 3. Rutas que SÍ tienen parámetros {id} (van al final)
Route::get('/doctores/{id}', [ControlDoctor::class, 'show'])->name('doctores.show');
Route::get('/doctores/{id}/editar', [ControlDoctor::class, 'edit'])->name('doctores.edit');
Route::put('/doctores/{id}', [ControlDoctor::class, 'update'])->name('doctores.update');
Route::delete('/doctores/{id}', [ControlDoctor::class, 'destroy'])->name('doctores.destroy');

// RUTAS DE PACIENTES (ejemplo de estructura)
Route::get('/pacientes', [ControlPaciente::class, 'index'])->name('pacientes.index');
Route::get('/pacientes/crear', [ControlPaciente::class, 'create'])->name('pacientes.create');
Route::post('/pacientes', [ControlPaciente::class, 'store'])->name('pacientes.store');
Route::get('/pacientes/{id}', [ControlPaciente::class, 'show'])->name('pacientes.show');
Route::get('/pacientes/{id}/editar', [ControlPaciente::class, 'edit'])->name('pacientes.edit');
Route::put('/pacientes/{id}', [ControlPaciente::class, 'update'])->name('pacientes.update');
Route::delete('/pacientes/{id}', [ControlPaciente::class, 'destroy'])->name('pacientes.destroy');

// RUTAS DE ADMIN (ejemplo)
Route::get('/admin', [ControlAdmin::class, 'index'])->name('admin.index');

// API ROUTES (si necesitas API separada)
Route::prefix('api')->group(function () {
    // Rutas API para doctores
    // Route::get('/doctores', [ControlDoctor::class, 'indexApi'])->name('api.doctores.index');
    // Route::get('/doctores/{id}', [ControlDoctor::class, 'showApi'])->name('api.doctores.show');
    
    // Ruta para datos reales en API
    if (env('APP_DEBUG')) {
        Route::get('/doctor/{id}/datos-reales', [ControlDoctor::class, 'datosReales'])
             ->name('api.doctores.datos.reales');
    }
});