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
Route::get('/formlogin', [ControlInicio::class, 'formlogin'])->name('ControlInicio.formlogin');
Route::post('/login', [ControlInicio::class, 'login'])->name('ControlInicio.login'); 
Route::post('/logout', [ControlInicio::class, 'logout'])->name('logout');
// RUTAS DEL LOGIN
Route::get('/medico', [ControlDoctor::class, 'dashboard'])->name('medico.dashboard');
Route::get('/paciente', [ControlPaciente::class, 'dashboard'])->name('paciente.dashboard');


// Rutas para el administrador
Route::get('/administrador', [ControlAdmin::class, 'dashboard'])->name('administrador.dashboard');
Route::get('/obtener-usuario-completo/{id}', [ControlAdmin::class, 'obtenerUsuarioCompleto'])->name('obtener.usuario.completo');
Route::post('/crear-usuario', [ControlAdmin::class, 'crearUsuario'])->name('crear.usuario');
Route::post('/actualizar-usuario/{id}', [ControlAdmin::class, 'actualizarUsuario'])->name('actualizar.usuario');
Route::get('/verificar-eliminar-usuario/{id}', [ControlAdmin::class, 'verificarEliminarUsuario'])->name('verificar.eliminar.usuario');
Route::delete('/eliminar-usuario/{id}', [ControlAdmin::class, 'eliminarUsuario'])->name('eliminar.usuario');