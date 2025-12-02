<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ControlInicio;
use App\Http\Controllers\Api\ControlAdmin;
use App\Http\Controllers\Api\ControlDoctor;
use App\Http\Controllers\Api\ControlPaciente;

// 🆕 IMPORTANTE: Agregar estos use del equipo
use App\Http\Controllers\Api\ConsultaController;
use App\Http\Controllers\Api\HabitacionController;
use App\Http\Controllers\Api\AreaController;
use App\Http\Controllers\Api\MedicoController;
use App\Http\Controllers\Api\PacienteController;

Route::get('/', function () {
    return view('pages.inicio');
});

// INICIO PAGE
Route::get('/medilab', [ControlInicio::class, 'index'])->name('ControlInicio.index'); 
Route::get('/main', [ControlInicio::class, 'main'])->name('ControlInicio.main'); 
Route::get('/login', [ControlInicio::class, 'login'])->name('ControlInicio.login'); 

// 🎯 **TUS RUTAS DE DOCTORES (CON CIFRADO AES)**
// 1. Rutas que NO tienen parámetros (van primero)
Route::get('/doctores', [ControlDoctor::class, 'index'])->name('doctores.index');
Route::get('/doctores/crear', [ControlDoctor::class, 'create'])->name('doctores.create');
Route::post('/doctores', [ControlDoctor::class, 'store'])->name('doctores.store');

// ✅ RUTA AGREGADA PARA VALIDACIÓN DE LICENCIA (TUYA)
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

// 🆕 **RUTAS DEL EQUIPO (otros módulos)**
Route::resource('consultas', ConsultaController::class)->parameters([
    'consultas' => 'consulta',
]);

Route::resource('habitaciones', HabitacionController::class)->parameters([
    'habitaciones' => 'habitacion',
]);

Route::resource('areas', AreaController::class);

Route::resource('medicos', MedicoController::class)->parameters([
    'medicos' => 'medico',
]);

Route::resource('pacientes', PacienteController::class)->parameters([
    'pacientes' => 'paciente',
]);

// RUTAS DE ADMIN (ejemplo)
Route::get('/admin', [ControlAdmin::class, 'index'])->name('admin.index');

// API ROUTES (si necesitas API separada)
Route::prefix('api')->group(function () {
    // Ruta para datos reales en API (TUYA)
    if (env('APP_DEBUG')) {
        Route::get('/doctor/{id}/datos-reales', [ControlDoctor::class, 'datosReales'])
             ->name('api.doctores.datos.reales');
    }
});