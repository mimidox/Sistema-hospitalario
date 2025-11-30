<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ControlInicio;
use App\Http\Controllers\Api\ControlAdmin;
use App\Http\Controllers\Api\ControlDoctor;
use App\Http\Controllers\Api\ControlPaciente;

// Agregado - Módulo de pacientes
use App\Http\Controllers\Api\ConsultaController;
use App\Http\Controllers\Api\HabitacionController;
use App\Http\Controllers\Api\AreaController;
use App\Http\Controllers\Api\MedicoController;
use App\Http\Controllers\Api\PacienteController;

// Model
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('pages.inicio');
});

// INICIO PAGE
Route::get('/medilab', [ControlInicio::class, 'index'])->name('ControlInicio.index'); 
Route::get('/main', [ControlInicio::class, 'main'])->name('ControlInicio.main'); 
Route::get('/formlogin', [ControlInicio::class, 'formlogin'])->name('ControlInicio.formlogin');
Route::post('/login', [ControlInicio::class, 'login'])->name('ControlInicio.login'); 
Route::post('/logout', [ControlInicio::class, 'logout'])->name('logout');

// RUTAS DEL LOGIN
Route::get('/medico', [ControlDoctor::class, 'dashboard'])->name('medico.dashboard');
Route::get('/paciente', [ControlPaciente::class, 'dashboard'])->name('paciente.dashboard');

// Rutas necesarias para consultas
Route::resource('consultas', ConsultaController::class)->parameters([
    'consultas' => 'consulta',
]);

// Rutas para el administrador
Route::get('/administrador', [ControlAdmin::class, 'dashboard'])->name('administrador.dashboard');
Route::get('/obtener-usuario-completo/{id}', [ControlAdmin::class, 'obtenerUsuarioCompleto'])->name('obtener.usuario.completo');
Route::post('/crear-usuario', [ControlAdmin::class, 'crearUsuario'])->name('crear.usuario');
Route::post('/actualizar-usuario/{id}', [ControlAdmin::class, 'actualizarUsuario'])->name('actualizar.usuario');
Route::get('/verificar-eliminar-usuario/{id}', [ControlAdmin::class, 'verificarEliminarUsuario'])->name('verificar.eliminar.usuario');
Route::delete('/eliminar-usuario/{id}', [ControlAdmin::class, 'eliminarUsuario'])->name('eliminar.usuario');

// Rutas del módulo de pacientes (nuevas)
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