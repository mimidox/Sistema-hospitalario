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

// RUTAS DEL LOGIN - CORREGIDAS
Route::get('/medico', [ControlDoctor::class, 'dashboard'])->name('medico.dashboard');
Route::get('/paciente', [ControlPaciente::class, 'dashboard'])->name('paciente.dashboard');

// CORRECCIÓN: Ruta para administrador - ESTA ES LA IMPORTANTE
Route::get('/administrador', [ControlInicio::class, 'administrador'])->name('administrador.dashboard');