<?php

use Illuminate\Support\Facades\Route;
// use Illuminate\Http\Request\ControlZona;
use App\Http\Controllers\Api\ControlInicio;
use App\Http\Controllers\Api\ControlAdmin;
use App\Http\Controllers\Api\ControlDoctor;
use App\Http\Controllers\Api\ControlPaciente;

// Agregado
use App\Http\Controllers\Api\ConsultaController;
use App\Http\Controllers\Api\HabitacionController;
use App\Http\Controllers\Api\AreaController;
use App\Http\Controllers\Api\MedicoController;
use App\Http\Controllers\Api\PacienteController;


//Model
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('pages.inicio');
});
//INICIO PAGE
Route::get('/medilab',[ControlInicio::class,'index'])->name('ControlInicio.index'); 
Route::get('/main',[ControlInicio::class,'main'])->name('ControlInicio.main'); 
Route::get('/login',[ControlInicio::class,'login'])->name('ControlInicio.login'); 

// Rutas necesarias
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

