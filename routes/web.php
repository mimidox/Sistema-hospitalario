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

// DASHBOARD del administrador
Route::get('/dashboard', [ControlAdmin::class, 'dashboard'])->name('administrador.dashboard');
// CRUD de usuarios
Route::get('/obtener-usuario-completo/{id}', [ControlAdmin::class, 'obtenerUsuarioCompleto'])->name('obtener.usuario.completo');
Route::post('/crear-usuario', [ControlAdmin::class, 'crearUsuario'])->name('crear.usuario');
Route::post('/actualizar-usuario/{id}', [ControlAdmin::class, 'actualizarUsuario'])->name('actualizar.usuario');
Route::get('/verificar-eliminar-usuario/{id}', [ControlAdmin::class, 'verificarEliminarUsuario'])->name('verificar.eliminar.usuario');
Route::delete('/eliminar-usuario/{id}', [ControlAdmin::class, 'eliminarUsuario'])->name('eliminar.usuario');
// Áreas
Route::get('/admin/areas/{areaId}/administradores', [ControlAdmin::class, 'getAdministradoresPorArea'])->name('admin.areas.administradores');
// Búsquedas
Route::get('/api/buscar/{tabla}', [ControlAdmin::class, 'buscarEnTabla'])->name('admin.buscar');
// ========== FUNCIONES DE LA TAREA ==========
Route::get('/probar-eliminacion/{id}', [ControlAdmin::class, 'probarEliminacionUsuario'])->name('probar.eliminacion');
Route::get('/probar-funciones', [ControlAdmin::class, 'probarFuncionesTarea'])->name('probar.funciones');
Route::get('/administrador/mejorado', [ControlAdmin::class, 'dashboardMejorado'])->name('administrador.mejorado');
    // ========== FUNCIONES DE LA TAREA (Funciones almacenadas, vistas, procedimientos) ==========

    // 1. Funciones almacenadas
    Route::get('/validar-username/{username}', [ControlAdmin::class, 'validarUsername'])->name('admin.validar.username');
    Route::get('/info-completa-administrador/{id}', [ControlAdmin::class, 'obtenerInfoCompleta'])->name('admin.info.completa');
    
    // 2. Vistas y reportes
    Route::get('/reporte/administradores', [ControlAdmin::class, 'reporteAdministradores'])->name('admin.reporte.administradores');
    Route::get('/reporte/actividades', [ControlAdmin::class, 'reporteActividades'])->name('admin.reporte.actividades');
    
    // 3. Cursores (procedimientos almacenados)
    Route::get('/ejecutar/estadisticas-areas', [ControlAdmin::class, 'ejecutarEstadisticasAreas'])->name('admin.ejecutar.estadisticas.areas');
    Route::get('/ejecutar/reporte-inactivos', [ControlAdmin::class, 'ejecutarReporteInactivos'])->name('admin.ejecutar.reporte.inactivos');
    
    // 4. Auditoría
    Route::get('/auditoria', [ControlAdmin::class, 'mostrarAuditoria'])->name('admin.auditoria');
    
    // 5. Procedimientos almacenados específicos
    Route::post('/crear-administrador-sp', [ControlAdmin::class, 'crearAdministradorConSP'])->name('admin.crear.administrador.sp');
    

// ========== RUTAS PÚBLICAS (sin protección) ==========
// (Agrega aquí solo las rutas que deben ser públicas)

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


//Doctor pagina
// Versión con resource controller

       
    Route::get('/medico', [ControlDoctor::class, 'dashboard'])->name('medico.dashboard');
    Route::get('/doctor', [ControlDoctor::class, 'dashboard'])->name('doctor');
    
    // Gestión de pacientes
    Route::get('/doctor/pacientes/{pacienteId}', [ControlDoctor::class, 'obtenerPacienteCompleto'])->name('doctor.obtener.paciente');
    Route::get('/doctor/buscar-pacientes', [ControlDoctor::class, 'buscarPacientes'])->name('doctor.buscar.pacientes');
    
    // Gestión de consultas
    Route::post('/doctor/consultas', [ControlDoctor::class, 'crearConsulta'])->name('doctor.crear.consulta');
    Route::post('/doctor/consultas/{consultaId}/atender', [ControlDoctor::class, 'atenderConsulta'])->name('doctor.atender.consulta');
    
    // Gestión de recetas
    Route::post('/doctor/recetas', [ControlDoctor::class, 'crearReceta'])->name('doctor.crear.receta');
    Route::get('/doctor/recetas/{recetaId}', [ControlDoctor::class, 'obtenerReceta'])->name('doctor.obtener.receta');
    
    // Gestión de hospitalizaciones
    Route::post('/doctor/hospitalizaciones/{hospitalizacionId}/alta', [ControlDoctor::class, 'darAltaPaciente'])->name('doctor.dar.alta');
    Route::get('/doctor/hospitalizaciones/{hospitalizacionId}/info', [ControlDoctor::class, 'obtenerInfoHospitalizacion'])->name('doctor.hospitalizacion.info');
    
    // Gestión de historiales
    Route::get('/doctor/historiales/{historialId}', [ControlDoctor::class, 'obtenerHistorialCompleto'])->name('doctor.obtener.historial');
    
    // Datos
    Route::get('/doctor/medicamentos', [ControlDoctor::class, 'obtenerMedicamentos'])->name('doctor.obtener.medicamentos');
