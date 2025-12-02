<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Receta; // Agregar este use
use App\Models\Medico; // Agregar este use

class ControlDoctor extends Controller
{
    // Dashboard del médico
    public function dashboard()
    {
        if (!session('logged_in') || session('rol') !== 'medico') {
            return redirect()->route('ControlInicio.formlogin')
                ->with('error', 'Debe iniciar sesión como médico');
        }

        try {
            $usuarioId = session('usuario_id');
            
            // Obtener información del médico
            $medico = DB::table('medico')->where('usuario_id', $usuarioId)->first();
            
            if (!$medico) {
                return redirect()->route('ControlInicio.formlogin')
                    ->with('error', 'No se encontró información médica');
            }

            // Consultas del día
            $consultasHoy = DB::table('consulta')
                ->join('ficha', 'consulta.ficha_id', '=', 'ficha.ficha_id')
                ->join('paciente', 'ficha.paciente_id', '=', 'paciente.paciente_id')
                ->join('usuario', 'paciente.usuario_id', '=', 'usuario.usuario_id')
                ->where('consulta.medico_id', $medico->medico_id)
                ->whereDate('consulta.fecha', today())
                ->select(
                    'consulta.*',
                    'ficha.nro_ficha',
                    'usuario.nombre',
                    'usuario.paterno',
                    'usuario.materno',
                    'paciente.paciente_id'
                )
                ->orderBy('consulta.fecha', 'asc')
                ->get();

            // Pacientes del médico (últimos 30 días)
            $pacientes = DB::table('consulta')
                ->join('ficha', 'consulta.ficha_id', '=', 'ficha.ficha_id')
                ->join('paciente', 'ficha.paciente_id', '=', 'paciente.paciente_id')
                ->join('usuario', 'paciente.usuario_id', '=', 'usuario.usuario_id')
                ->leftJoin('historial_medico', 'paciente.paciente_id', '=', 'historial_medico.paciente_id')
                ->where('consulta.medico_id', $medico->medico_id)
                ->whereDate('consulta.fecha', '>=', now()->subDays(30))
                ->select(
                    'paciente.paciente_id',
                    'usuario.username',
                    'usuario.nombre',
                    'usuario.paterno',
                    'usuario.materno',
                    'usuario.correo',
                    'usuario.telefono',
                    'usuario.genero',
                    'usuario.fec_nac',
                    'paciente.tipo_de_sangre',
                    'historial_medico.diagnosticos',
                    DB::raw('MAX(consulta.fecha) as ultima_consulta')
                )
                ->groupBy(
                    'paciente.paciente_id', 
                    'usuario.username',
                    'usuario.nombre',
                    'usuario.paterno',
                    'usuario.materno',
                    'usuario.correo',
                    'usuario.telefono',
                    'usuario.genero',
                    'usuario.fec_nac',
                    'paciente.tipo_de_sangre',
                    'historial_medico.diagnosticos'
                )
                ->get();

            // Hospitalizaciones activas
            $hospitalizaciones = DB::table('hospitalizacion')
                ->join('paciente', 'hospitalizacion.paciente_id', '=', 'paciente.paciente_id')
                ->join('usuario', 'paciente.usuario_id', '=', 'usuario.usuario_id')
                ->join('habitacion', 'hospitalizacion.habitacion_id', '=', 'habitacion.habitacion_id')
                ->where('hospitalizacion.medico_id', $medico->medico_id)
                ->whereNull('hospitalizacion.fecha_alta')
                ->select(
                    'hospitalizacion.*',
                    'usuario.nombre',
                    'usuario.paterno',
                    'usuario.materno',
                    'habitacion.nro_habitacion',
                    'habitacion.tipo as tipo_habitacion'
                )
                ->get();

            // Historiales médicos recientes
            $historiales = DB::table('historial_medico')
                ->join('paciente', 'historial_medico.paciente_id', '=', 'paciente.paciente_id')
                ->join('usuario', 'paciente.usuario_id', '=', 'usuario.usuario_id')
                ->whereIn('paciente.paciente_id', function($query) use ($medico) {
                    $query->select('ficha.paciente_id')
                        ->from('consulta')
                        ->join('ficha', 'consulta.ficha_id', '=', 'ficha.ficha_id')
                        ->where('consulta.medico_id', $medico->medico_id);
                })
                ->select(
                    'historial_medico.*',
                    'usuario.nombre',
                    'usuario.paterno',
                    'usuario.materno'
                )
                ->orderBy('historial_medico.historial_id', 'desc')
                ->limit(20)
                ->get();

            // Recetas emitidas - CORREGIDO (usando DB::table en lugar de Eloquent)
            // En el método dashboard, reemplaza la consulta de recetas por:
            $recetas = Receta::with([
                    'medicamento',
                    'tratamiento.historial.paciente.usuario'
                ])
                ->whereHas('tratamiento.historial.paciente.consultas', function($query) use ($medico) {
                    $query->where('medico_id', $medico->medico_id);
                })
                ->orderBy('receta_id', 'desc')
                ->limit(20)
                ->get();

            // En el método dashboard del ControlDoctor, agrega:
            $medicamentos = DB::table('medicamentos')->get();

            return view('pages.logins.doctor', compact(
                'consultasHoy',
                'pacientes', 
                'hospitalizaciones',
                'historiales',
                'recetas',
                'medico',
                'pacientes',
                'medicamentos' // Agregar esta línea
            ));
            
            return view('pages.logins.doctor', compact(
                'consultasHoy',
                'pacientes', 
                'hospitalizaciones',
                'historiales',
                'recetas',
                'medico'
            ));

        } catch (\Exception $e) {
            Log::error('Error en dashboard médico: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar el dashboard médico');
        }
    }

    // Obtener datos completos de un paciente
    public function obtenerPacienteCompleto($pacienteId)
    {
        if (!session('logged_in') || session('rol') !== 'medico') {
            return response()->json([
                'success' => false, 
                'message' => 'No autorizado'
            ], 401);
        }

        try {
            // Obtener paciente con toda su información
            $paciente = DB::table('paciente')
                ->join('usuario', 'paciente.usuario_id', '=', 'usuario.usuario_id')
                ->leftJoin('historial_medico', 'paciente.paciente_id', '=', 'historial_medico.paciente_id')
                ->where('paciente.paciente_id', $pacienteId)
                ->select(
                    'paciente.*',
                    'usuario.*',
                    'historial_medico.antecedentes',
                    'historial_medico.alergias',
                    'historial_medico.diagnosticos'
                )
                ->first();

            if (!$paciente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Paciente no encontrado'
                ], 404);
            }

            // Obtener consultas recientes del paciente
            $consultas = DB::table('consulta')
                ->join('ficha', 'consulta.ficha_id', '=', 'ficha.ficha_id')
                ->join('medico', 'consulta.medico_id', '=', 'medico.medico_id')
                ->join('usuario as medico_usuario', 'medico.usuario_id', '=', 'medico_usuario.usuario_id')
                ->where('ficha.paciente_id', $pacienteId)
                ->select(
                    'consulta.*',
                    'medico_usuario.nombre as medico_nombre',
                    'medico_usuario.paterno as medico_paterno',
                    'medico.especialidad'
                )
                ->orderBy('consulta.fecha', 'desc')
                ->limit(10)
                ->get();

            // Obtener tratamientos recientes
            $tratamientos = DB::table('tratamiento')
                ->join('historial_medico', 'tratamiento.historial_id', '=', 'historial_medico.historial_id')
                ->where('historial_medico.paciente_id', $pacienteId)
                ->select('tratamiento.*')
                ->orderBy('tratamiento.fecha_ini', 'desc')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'paciente' => $paciente,
                'consultas' => $consultas,
                'tratamientos' => $tratamientos
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener paciente: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar paciente'
            ], 500);
        }
    }

    // Crear nueva consulta
    public function crearConsulta(Request $request)
    {
        if (!session('logged_in') || session('rol') !== 'medico') {
            return response()->json([
                'success' => false, 
                'message' => 'No autorizado'
            ], 401);
        }

        try {
            DB::beginTransaction();

            $medico = DB::table('medico')->where('usuario_id', session('usuario_id'))->first();
            
            // Validar datos
            $request->validate([
                'paciente_id' => 'required|exists:paciente,paciente_id',
                'motivo_consulta' => 'required|string|max:255',
                'tipo_consulta' => 'required|string|max:50',
                'fecha_consulta' => 'required|date',
                'hora_consulta' => 'required'
            ]);

            // Crear ficha
            $fichaId = DB::table('ficha')->insertGetId([
                'paciente_id' => $request->paciente_id,
                'seguro_id' => 1, // Seguro por defecto
                'fecha' => $request->fecha_consulta,
                'hora' => $request->hora_consulta,
                'estado' => 'activa',
                'nro_ficha' => 'F' . time()
            ]);

            // Crear consulta
            $consultaId = DB::table('consulta')->insertGetId([
                'medico_id' => $medico->medico_id,
                'ficha_id' => $fichaId,
                'motivo_consulta' => $request->motivo_consulta,
                'fecha' => $request->fecha_consulta,
                'tipo' => $request->tipo_consulta
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Consulta creada exitosamente',
                'consulta_id' => $consultaId
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false, 
                'message' => 'Error de validación: ' . $e->getMessage(),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear consulta: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error al crear consulta'
            ], 500);
        }
    }

    // Atender consulta
    public function atenderConsulta(Request $request, $consultaId)
    {
        if (!session('logged_in') || session('rol') !== 'medico') {
            return response()->json([
                'success' => false, 
                'message' => 'No autorizado'
            ], 401);
        }

        try {
            $consulta = DB::table('consulta')
                ->join('ficha', 'consulta.ficha_id', '=', 'ficha.ficha_id')
                ->join('paciente', 'ficha.paciente_id', '=', 'paciente.paciente_id')
                ->join('usuario', 'paciente.usuario_id', '=', 'usuario.usuario_id')
                ->where('consulta.consulta_id', $consultaId)
                ->select(
                    'consulta.*',
                    'paciente.paciente_id',
                    'usuario.nombre',
                    'usuario.paterno',
                    'usuario.materno'
                )
                ->first();

            if (!$consulta) {
                return response()->json([
                    'success' => false,
                    'message' => 'Consulta no encontrada'
                ], 404);
            }

            // Verificar que el médico es el asignado a la consulta
            $medico = DB::table('medico')->where('usuario_id', session('usuario_id'))->first();
            if ($consulta->medico_id !== $medico->medico_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene permisos para atender esta consulta'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'message' => 'Iniciando atención de consulta',
                'consulta' => $consulta
            ]);

        } catch (\Exception $e) {
            Log::error('Error al atender consulta: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error al atender consulta'
            ], 500);
        }
    }

    // Crear receta médica
    public function crearReceta(Request $request)
    {
        if (!session('logged_in') || session('rol') !== 'medico') {
            return response()->json([
                'success' => false, 
                'message' => 'No autorizado'
            ], 401);
        }

        try {
            DB::beginTransaction();

            $request->validate([
                'paciente_id' => 'required|exists:paciente,paciente_id',
                'medicamentos' => 'required|array',
                'medicamentos.*.medicamento_id' => 'required|exists:medicamentos,medicamento_id',
                'medicamentos.*.dosis' => 'required|string|max:100',
                'medicamentos.*.duracion' => 'required|string|max:100',
                'medicamentos.*.frecuencia' => 'required|string|max:100',
                'diagnostico' => 'required|string'
            ]);

            // Obtener o crear historial médico
            $historial = DB::table('historial_medico')
                ->where('paciente_id', $request->paciente_id)
                ->first();

            if (!$historial) {
                $historialId = DB::table('historial_medico')->insertGetId([
                    'paciente_id' => $request->paciente_id,
                    'antecedentes' => '',
                    'alergias' => '',
                    'diagnosticos' => $request->diagnostico
                ]);
            } else {
                $historialId = $historial->historial_id;
                // Actualizar diagnóstico
                $nuevosDiagnosticos = $historial->diagnosticos ? $historial->diagnosticos . '; ' . $request->diagnostico : $request->diagnostico;
                DB::table('historial_medico')
                    ->where('historial_id', $historialId)
                    ->update(['diagnosticos' => $nuevosDiagnosticos]);
            }

            // Crear tratamiento
            $tratamientoId = DB::table('tratamiento')->insertGetId([
                'historial_id' => $historialId,
                'tipo_tratamiento' => 'Farmacológico',
                'fecha_ini' => now(),
                'fecha_fin' => now()->addDays(7)
            ]);

            // Crear recetas
            foreach ($request->medicamentos as $med) {
                DB::table('receta')->insert([
                    'tratamiento_id' => $tratamientoId,
                    'medicamento_id' => $med['medicamento_id'],
                    'dosis' => $med['dosis'],
                    'duracion' => $med['duracion'],
                    'frecuencia' => $med['frecuencia']
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Receta médica creada exitosamente',
                'tratamiento_id' => $tratamientoId
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false, 
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear receta: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error al crear receta: ' . $e->getMessage()
            ], 500);
        }
    }

    // Dar de alta a paciente hospitalizado
    public function darAltaPaciente(Request $request, $hospitalizacionId)
    {
        if (!session('logged_in') || session('rol') !== 'medico') {
            return response()->json([
                'success' => false, 
                'message' => 'No autorizado'
            ], 401);
        }

        try {
            $hospitalizacion = DB::table('hospitalizacion')
                ->where('hospitalizacion_id', $hospitalizacionId)
                ->first();

            if (!$hospitalizacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hospitalización no encontrada'
                ], 404);
            }

            // Verificar que el médico tiene permisos
            $medico = DB::table('medico')->where('usuario_id', session('usuario_id'))->first();
            if ($hospitalizacion->medico_id !== $medico->medico_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene permisos para dar de alta este paciente'
                ], 403);
            }

            DB::beginTransaction();

            // Actualizar hospitalización
            DB::table('hospitalizacion')
                ->where('hospitalizacion_id', $hospitalizacionId)
                ->update([
                    'fecha_alta' => now(),
                    'diagnostico' => $request->diagnostico_final ?? $hospitalizacion->diagnostico
                ]);

            // Liberar habitación
            DB::table('habitacion')
                ->where('habitacion_id', $hospitalizacion->habitacion_id)
                ->update(['estado' => 'Libre']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Paciente dado de alta exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al dar de alta: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error al dar de alta'
            ], 500);
        }
    }

    // Obtener lista de medicamentos
    public function obtenerMedicamentos()
    {
        if (!session('logged_in') || session('rol') !== 'medico') {
            return response()->json([
                'success' => false, 
                'message' => 'No autorizado'
            ], 401);
        }

        try {
            $medicamentos = DB::table('medicamentos')->get();
            
            return response()->json([
                'success' => true,
                'medicamentos' => $medicamentos
            ]);
        } catch (\Exception $e) {
            Log::error('Error al cargar medicamentos: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar medicamentos'
            ], 500);
        }
    }

    // Buscar pacientes
    public function buscarPacientes(Request $request)
    {
        if (!session('logged_in') || session('rol') !== 'medico') {
            return response()->json([
                'success' => false, 
                'message' => 'No autorizado'
            ], 401);
        }

        try {
            $query = $request->get('q');
            
            $pacientes = DB::table('paciente')
                ->join('usuario', 'paciente.usuario_id', '=', 'usuario.usuario_id')
                ->where(function($q) use ($query) {
                    $q->where('usuario.nombre', 'LIKE', "%{$query}%")
                      ->orWhere('usuario.paterno', 'LIKE', "%{$query}%")
                      ->orWhere('usuario.materno', 'LIKE', "%{$query}%")
                      ->orWhere('usuario.username', 'LIKE', "%{$query}%");
                })
                ->select(
                    'paciente.paciente_id',
                    'usuario.nombre',
                    'usuario.paterno',
                    'usuario.materno',
                    'usuario.username'
                )
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'pacientes' => $pacientes
            ]);
        } catch (\Exception $e) {
            Log::error('Error en búsqueda de pacientes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error en la búsqueda'
            ], 500);
        }
    }

    // Obtener estadísticas del médico
    public function obtenerEstadisticas()
    {
        if (!session('logged_in') || session('rol') !== 'medico') {
            return response()->json([
                'success' => false, 
                'message' => 'No autorizado'
            ], 401);
        }

        try {
            $medico = DB::table('medico')->where('usuario_id', session('usuario_id'))->first();
            
            $estadisticas = [
                'consultas_hoy' => DB::table('consulta')
                    ->where('medico_id', $medico->medico_id)
                    ->whereDate('fecha', today())
                    ->count(),
                'consultas_mes' => DB::table('consulta')
                    ->where('medico_id', $medico->medico_id)
                    ->whereMonth('fecha', now()->month)
                    ->count(),
                'pacientes_activos' => DB::table('consulta')
                    ->join('ficha', 'consulta.ficha_id', '=', 'ficha.ficha_id')
                    ->where('consulta.medico_id', $medico->medico_id)
                    ->whereDate('consulta.fecha', '>=', now()->subDays(30))
                    ->distinct('ficha.paciente_id')
                    ->count('ficha.paciente_id'),
                'hospitalizados' => DB::table('hospitalizacion')
                    ->where('medico_id', $medico->medico_id)
                    ->whereNull('fecha_alta')
                    ->count()
            ];

            return response()->json([
                'success' => true,
                'estadisticas' => $estadisticas
            ]);
        } catch (\Exception $e) {
            Log::error('Error al cargar estadísticas: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar estadísticas'
            ], 500);
        }
    }
    // En ControlDoctor.php agrega estos métodos:

/**
 * Obtener información de hospitalización
 */
    public function obtenerInfoHospitalizacion($hospitalizacionId)
    {
        if (!session('logged_in') || session('rol') !== 'medico') {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 401);
        }

        try {
            $hospitalizacion = DB::table('hospitalizacion')
                ->join('paciente', 'hospitalizacion.paciente_id', '=', 'paciente.paciente_id')
                ->join('usuario', 'paciente.usuario_id', '=', 'usuario.usuario_id')
                ->join('habitacion', 'hospitalizacion.habitacion_id', '=', 'habitacion.habitacion_id')
                ->where('hospitalizacion.hospitalizacion_id', $hospitalizacionId)
                ->select(
                    'hospitalizacion.*',
                    'usuario.nombre',
                    'usuario.paterno',
                    'habitacion.nro_habitacion'
                )
                ->first();

            if (!$hospitalizacion) {
                return response()->json(['success' => false, 'message' => 'Hospitalización no encontrada'], 404);
            }

            return response()->json([
                'success' => true,
                'paciente' => [
                    'nombre' => $hospitalizacion->nombre,
                    'paterno' => $hospitalizacion->paterno
                ],
                'habitacion' => [
                    'nro_habitacion' => $hospitalizacion->nro_habitacion
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener info hospitalización: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al cargar información'], 500);
        }
    }

    /**
     * Obtener receta específica
     */
    public function obtenerReceta($recetaId)
    {
        if (!session('logged_in') || session('rol') !== 'medico') {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 401);
        }

        try {
            $receta = DB::table('receta')
                ->join('medicamentos', 'receta.medicamento_id', '=', 'medicamentos.medicamento_id')
                ->join('tratamiento', 'receta.tratamiento_id', '=', 'tratamiento.tratamiento_id')
                ->join('historial_medico', 'tratamiento.historial_id', '=', 'historial_medico.historial_id')
                ->join('paciente', 'historial_medico.paciente_id', '=', 'paciente.paciente_id')
                ->join('usuario', 'paciente.usuario_id', '=', 'usuario.usuario_id')
                ->where('receta.receta_id', $recetaId)
                ->select(
                    'receta.*',
                    'medicamentos.nombre as medicamento_nombre',
                    'usuario.nombre as paciente_nombre',
                    'usuario.paterno as paciente_paterno'
                )
                ->first();

            if (!$receta) {
                return response()->json(['success' => false, 'message' => 'Receta no encontrada'], 404);
            }

            return response()->json([
                'success' => true,
                'receta' => $receta
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener receta: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al cargar receta'], 500);
        }
    }

    /**
     * Obtener historial completo
     */
    public function obtenerHistorialCompleto($historialId)
    {
        if (!session('logged_in') || session('rol') !== 'medico') {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 401);
        }

        try {
            $historial = DB::table('historial_medico')
                ->join('paciente', 'historial_medico.paciente_id', '=', 'paciente.paciente_id')
                ->join('usuario', 'paciente.usuario_id', '=', 'usuario.usuario_id')
                ->where('historial_medico.historial_id', $historialId)
                ->select(
                    'historial_medico.*',
                    'usuario.nombre',
                    'usuario.paterno'
                )
                ->first();

            if (!$historial) {
                return response()->json(['success' => false, 'message' => 'Historial no encontrado'], 404);
            }

            return response()->json([
                'success' => true,
                'historial' => $historial
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener historial: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al cargar historial'], 500);
        }
    }
}