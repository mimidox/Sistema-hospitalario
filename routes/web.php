<?php

use Illuminate\Support\Facades\Route;
// use Illuminate\Http\Request\ControlZona;
use App\Http\Controllers\Api\ControlInicio;
use App\Http\Controllers\Api\ControlAdmin;
use App\Http\Controllers\Api\ControlDoctor;
use App\Http\Controllers\Api\ControlPaciente;

//Model
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});
//INICIO PAGE
Route::get('/medilab',[ControlInicio::class,'index'])->name('ControlInicio.index'); 
Route::get('/main',[ControlInicio::class,'main'])->name('ControlInicio.main'); 
Route::get('/login',[ControlInicio::class,'login'])->name('ControlInicio.login'); 


