<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ControlAdmin extends Controller
{
   // Temporalmente en tu ControlAdmin, agrega esto:

    public function mostrarAuditoria(Request $request)
    {
        // Verificación de sesión
        if (!session('logged_in') || session('rol') !== 'administrativo') {
            return response()->json([
                'success' => false, 
                'message' => 'No autorizado'
            ], 401);
        }

        try {
            $dias = $request->get('dias', 30);
            
            // Consulta principal
            $query = DB::table('auditoria')
                ->join('usuario', 'auditoria.usuario_id', '=', 'usuario.usuario_id')
                ->select(
                    'auditoria.*',
                    'usuario.username',
                    'usuario.nombre',
                    'usuario.paterno',
                    'usuario.materno',
                    DB::raw('CONCAT(usuario.nombre, " ", usuario.paterno) as nombre_completo')
                );
            
            // Aplicar filtro de días si no es 'all'
            if ($dias !== 'all' && is_numeric($dias)) {
                $query->where('auditoria.fecha', '>=', now()->subDays($dias));
            }
            
            // Ordenar por fecha descendente
            $query->orderBy('auditoria.fecha', 'desc');
            
            // Obtener todos los registros (sin paginación porque el JS lo maneja)
            $auditoria = $query->get();
            
            // IMPORTANTE: Asegúrate de que los campos IP existan
            // Si no tienes ip_address en la tabla, puedes omitirlo
            
            return response()->json([
                'success' => true,
                'auditoria' => $auditoria,
                'total_registros' => count($auditoria),
                'filtro_dias' => $dias,
                'fecha_consulta' => now()->format('Y-m-d H:i:s')
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al obtener auditoría: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error al cargar auditoría: ' . $e->getMessage()
            ], 500);
        }
    }


    // Verificar si se puede eliminar usuario - MEJORADO
    public function verificarEliminarUsuario($id)
    {
        if (!session('logged_in') || session('rol') !== 'administrativo') {
            return response()->json([
                'success' => false, 
                'message' => 'No autorizado'
            ], 401);
        }

        try {
            // Verificar que el usuario existe
            $usuario = DB::table('usuario')->where('usuario_id', $id)->first();
            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            $enPaciente = DB::table('paciente')->where('usuario_id', $id)->exists();
            $enMedico = DB::table('medico')->where('usuario_id', $id)->exists();
            $enAdministrativo = DB::table('administrativo')->where('usuario_id', $id)->exists();

            $referencias = [];
            if ($enPaciente) $referencias[] = 'Paciente';
            if ($enMedico) $referencias[] = 'Médico';
            if ($enAdministrativo) $referencias[] = 'Administrativo';

            $puedeEliminar = empty($referencias);

            return response()->json([
                'success' => true,
                'puede_eliminar' => $puedeEliminar,
                'referencias' => $referencias,
                'mensaje' => $puedeEliminar 
                    ? '¿Está seguro de eliminar este usuario?' 
                    : 'No se puede eliminar el usuario porque está referenciado en: ' . implode(', ', $referencias)
            ]);

        } catch (\Exception $e) {
            Log::error('Error al verificar eliminación: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error al verificar eliminación'
            ], 500);
        }
    }

    // Eliminar usuario - MEJORADO
    public function eliminarUsuario($id)
    {
        if (!session('logged_in') || session('rol') !== 'administrativo') {
            return response()->json([
                'success' => false, 
                'message' => 'No autorizado'
            ], 401);
        }

        try {
            // Verificar que el usuario existe
            $usuario = DB::table('usuario')->where('usuario_id', $id)->first();
            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            // Verificar nuevamente que no tenga referencias (doble seguridad)
            $tieneReferencias = DB::table('paciente')->where('usuario_id', $id)->exists() ||
                               DB::table('medico')->where('usuario_id', $id)->exists() ||
                               DB::table('administrativo')->where('usuario_id', $id)->exists();

            if ($tieneReferencias) {
                return response()->json([
                    'success' => false, 
                    'message' => 'No se puede eliminar el usuario porque tiene referencias activas'
                ], 400);
            }

            $eliminado = DB::table('usuario')->where('usuario_id', $id)->delete();

            if ($eliminado) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Usuario eliminado exitosamente'
                ]);
            } else {
                return response()->json([
                    'success' => false, 
                    'message' => 'No se pudo eliminar el usuario'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error al eliminar usuario: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error al eliminar usuario'
            ], 500);
        }
    }
    /// AREA
    public function getAdministradoresPorArea($areaId)
    {
        if (!session('logged_in') || session('rol') !== 'administrativo') {
            return response()->json([
                'success' => false, 
                'message' => 'No autorizado'
            ], 401);
        }

        try {
            \Log::info("Buscando administradores para área: " . $areaId);

            // Obtener área
            $area = DB::table('area')->where('area_id', $areaId)->first();
            
            if (!$area) {
                \Log::warning("Área no encontrada: " . $areaId);
                return response()->json([
                    'success' => false,
                    'message' => 'Área no encontrada'
                ], 404);
            }

            // Obtener TODOS los administradores del área para el contador
            $totalAdministradores = DB::table('administrativo')
                ->where('area_id', $areaId)
                ->count();

            \Log::info("Total administradores en área {$areaId}: " . $totalAdministradores);

            // Obtener solo los últimos 4 administradores agregados
            $ultimosAdministradores = DB::table('administrativo')
                ->join('usuario', 'administrativo.usuario_id', '=', 'usuario.usuario_id')
                ->leftJoin('area', 'administrativo.area_id', '=', 'area.area_id')
                ->where('administrativo.area_id', $areaId)
                ->select(
                    'administrativo.administrativo_id',
                    'administrativo.cargo',
                    'administrativo.area_id',
                    'usuario.nombre',
                    'usuario.paterno', 
                    'usuario.materno',
                    'usuario.correo as email',
                    'usuario.telefono',
                    'area.nombre_area'
                )
                ->orderBy('administrativo.administrativo_id', 'desc')
                ->limit(4)
                ->get()
                ->map(function($admin) {
                    return [
                        'id' => $admin->administrativo_id,
                        'nombre_completo' => trim($admin->nombre . ' ' . $admin->paterno . ' ' . ($admin->materno ?? '')),
                        'cargo' => $admin->cargo,
                        'email' => $admin->email,
                        'telefono' => $admin->telefono,
                        'area' => $admin->nombre_area,
                        'fecha_ingreso' => 'Reciente'
                    ];
                });

            \Log::info("Últimos administradores encontrados: " . $ultimosAdministradores->count());

            return response()->json([
                'success' => true,
                'area' => [
                    'nombre' => $area->nombre_area,
                    'descripcion' => $area->descripcion
                ],
                'total_administradores' => $totalAdministradores,
                'ultimos_administradores' => $ultimosAdministradores,
                'mostrando_ultimos' => 4
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al obtener administradores por área: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }
    public function buscarEnTabla(Request $request, $tabla)
    {
        if (!session('logged_in') || session('rol') !== 'administrativo') {
            return response()->json([
                'success' => false, 
                'message' => 'No autorizado'
            ], 401);
        }

        try {
            $nombre = $request->get('nombre');
            
            if (!$nombre) {
                return response()->json([
                    'success' => false,
                    'message' => 'El parámetro nombre es requerido'
                ], 400);
            }

            $resultados = [];

            switch ($tabla) {
                case 'usuarios':
                    $resultados = DB::table('usuario')
                        ->where('nombre', 'LIKE', "%{$nombre}%")
                        ->orWhere('paterno', 'LIKE', "%{$nombre}%")
                        ->orWhere('materno', 'LIKE', "%{$nombre}%")
                        ->orWhere('username', 'LIKE', "%{$nombre}%")
                        ->get();
                    break;

                case 'pacientes':
                    $resultados = DB::table('paciente')
                        ->join('usuario', 'paciente.usuario_id', '=', 'usuario.usuario_id')
                        ->select('paciente.*', 'usuario.nombre', 'usuario.paterno', 'usuario.materno', 'usuario.correo', 'usuario.telefono')
                        ->where(function($query) use ($nombre) {
                            $query->where('usuario.nombre', 'LIKE', "%{$nombre}%")
                                ->orWhere('usuario.paterno', 'LIKE', "%{$nombre}%")
                                ->orWhere('usuario.materno', 'LIKE', "%{$nombre}%");
                        })
                        ->get();
                    break;

                case 'medicos':
                    $resultados = DB::table('medico')
                        ->join('usuario', 'medico.usuario_id', '=', 'usuario.usuario_id')
                        ->select('medico.*', 'usuario.nombre', 'usuario.paterno', 'usuario.materno', 'usuario.correo')
                        ->where(function($query) use ($nombre) {
                            $query->where('usuario.nombre', 'LIKE', "%{$nombre}%")
                                ->orWhere('usuario.paterno', 'LIKE', "%{$nombre}%")
                                ->orWhere('usuario.materno', 'LIKE', "%{$nombre}%")
                                ->orWhere('medico.especialidad', 'LIKE', "%{$nombre}%");
                        })
                        ->get();
                    break;

                case 'administrativos':
                    $resultados = DB::table('administrativo')
                        ->join('usuario', 'administrativo.usuario_id', '=', 'usuario.usuario_id')
                        ->leftJoin('area', 'administrativo.area_id', '=', 'area.area_id')
                        ->select('administrativo.*', 'usuario.nombre', 'usuario.paterno', 'usuario.materno', 'usuario.correo', 'area.nombre_area')
                        ->where(function($query) use ($nombre) {
                            $query->where('usuario.nombre', 'LIKE', "%{$nombre}%")
                                ->orWhere('usuario.paterno', 'LIKE', "%{$nombre}%")
                                ->orWhere('usuario.materno', 'LIKE', "%{$nombre}%")
                                ->orWhere('administrativo.cargo', 'LIKE', "%{$nombre}%");
                        })
                        ->get();
                    break;

                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Tabla no válida'
                    ], 400);
            }

            return response()->json([
                'success' => true,
                'resultados' => $resultados,
                'total' => count($resultados)
            ]);

        } catch (\Exception $e) {
            Log::error('Error en búsqueda: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al realizar la búsqueda: ' . $e->getMessage()
            ], 500);
        }
    }
    //NUEVOOOOOOOOOOOOOOOOOOOOOOOOOOOS
    public function obtenerUsuarioCompleto($id)
    {
        if (!session('logged_in') || session('rol') !== 'administrativo') {
            return response()->json([
                'success' => false, 
                'message' => 'No autorizado'
            ], 401);
        }

        try {
            // Obtener usuario base
            $usuario = DB::table('usuario')
                ->where('usuario_id', $id)
                ->where('estado', 'activo')
                ->first();
            
            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            // Verificar roles del usuario
            $roles = [
                'medico' => DB::table('medico')->where('usuario_id', $id)->first(),
                'paciente' => DB::table('paciente')->where('usuario_id', $id)->first(),
                'administrativo' => DB::table('administrativo')
                    ->leftJoin('area', 'administrativo.area_id', '=', 'area.area_id')
                    ->where('administrativo.usuario_id', $id)
                    ->select('administrativo.*', 'area.nombre_area')
                    ->first()
            ];

            // Filtrar roles nulos
            $roles = array_filter($roles);

            return response()->json([
                'success' => true,
                'usuario' => $usuario,
                'roles' => $roles
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener usuario: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar usuario'
            ], 500);
        }
    }

    // Crear usuario - COMPLETAMENTE CORREGIDO
    public function crearUsuario(Request $request)
    {
        if (!session('logged_in') || session('rol') !== 'administrativo') {
            return response()->json([
                'success' => false, 
                'message' => 'No autorizado'
            ], 401);
        }

        try {
            // Validar campos requeridos
            $validator = Validator::make($request->all(), [
                'username' => 'required|unique:usuario,username',
                'nombre' => 'required|string|max:50',
                'paterno' => 'required|string|max:50',
                'materno' => 'nullable|string|max:50',
                'correo' => 'required|email|unique:usuario,correo',
                'password' => 'required|min:6',
                'genero' => 'nullable|in:M,F,O',
                'telefono' => 'nullable|string|max:20',
                'rol' => 'required|in:medico,paciente,administrativo'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            // Encriptar contraseña con SHA2 (como en tu base de datos)
            $passwordHash = hash('sha256', $request->password);

            // Crear usuario base
            $usuarioId = DB::table('usuario')->insertGetId([
                'username' => $request->username,
                'nombre' => $request->nombre,
                'paterno' => $request->paterno,
                'materno' => $request->materno ?? null,
                'genero' => $request->genero ?? 'M',
                'correo' => $request->correo,
                'contraseña' => $passwordHash, // CORREGIDO: usar hash
                'telefono' => $request->telefono ?? null,
                'calle' => $request->calle ?? null,
                'zona' => $request->zona ?? null,
                'municipio' => $request->municipio ?? null,
                'fec_nac' => $request->fec_nac ?? null,
                'estado' => 'activo',
                'fecha_registro' => now(),
                'fecha_actualizacion' => now()
            ]);

            // Crear rol según selección
            switch ($request->rol) {
                case 'paciente':
                    DB::table('paciente')->insert([
                        'usuario_id' => $usuarioId,
                        'tipo_de_sangre' => $request->tipo_sangre ?? null
                    ]);
                    break;
                    
                case 'medico':
                    DB::table('medico')->insert([
                        'usuario_id' => $usuarioId,
                        'especialidad' => $request->especialidad ?? 'General',
                        'nro_licencia' => $request->nro_licencia ?? 'LIC_' . rand(1000, 9999),
                        'años_experiencia' => $request->años_experiencia ?? 0
                    ]);
                    break;
                    
                case 'administrativo':
                    DB::table('administrativo')->insert([
                        'usuario_id' => $usuarioId,
                        'cargo' => $request->cargo ?? 'Empleado',
                        'area_id' => $request->area_id ?? 1
                    ]);
                    break;
            }

            // Registrar en auditoría
            DB::table('auditoria')->insert([
                'usuario_id' => session('usuario_id'),
                'accion' => 'Creó usuario ID: ' . $usuarioId,
                'fecha' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Usuario creado exitosamente',
                'usuario_id' => $usuarioId,
                'rol' => $request->rol
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
            Log::error('Error al crear usuario: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error al crear usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    // Actualizar usuario - COMPLETAMENTE REESCRITO Y CORREGIDO
    public function actualizarUsuario(Request $request, $id)
    {
        if (!session('logged_in') || session('rol') !== 'administrativo') {
            return response()->json([
                'success' => false, 
                'message' => 'No autorizado'
            ], 401);
        }

        try {
            // Validar que el usuario existe
            $usuario = DB::table('usuario')
                ->where('usuario_id', $id)
                ->first();
                
            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            // Validar campos únicos excluyendo el usuario actual
            $validator = Validator::make($request->all(), [
                'username' => 'required|unique:usuario,username,' . $id . ',usuario_id',
                'correo' => 'required|email|unique:usuario,correo,' . $id . ',usuario_id',
                'nombre' => 'required|string|max:50',
                'paterno' => 'required|string|max:50',
                'materno' => 'nullable|string|max:50',
                'password' => 'nullable|min:6',
                'genero' => 'nullable|in:M,F,O',
                'telefono' => 'nullable|string|max:20',
                'rol' => 'nullable|in:medico,paciente,administrativo'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            // Preparar datos para actualizar usuario base
            $datosActualizar = [
                'username' => $request->username,
                'nombre' => $request->nombre,
                'paterno' => $request->paterno,
                'materno' => $request->materno ?? null,
                'genero' => $request->genero ?? 'M',
                'correo' => $request->correo,
                'telefono' => $request->telefono ?? null,
                'calle' => $request->calle ?? null,
                'zona' => $request->zona ?? null,
                'municipio' => $request->municipio ?? null,
                'fec_nac' => $request->fec_nac ?? null,
                'fecha_actualizacion' => now()
            ];

            // Si se proporciona una nueva contraseña, cifrarla con SHA2
            if ($request->filled('password') && !empty($request->password)) {
                $datosActualizar['contraseña'] = hash('sha256', $request->password);
            }

            // Actualizar usuario base
            DB::table('usuario')
                ->where('usuario_id', $id)
                ->update($datosActualizar);

            // GESTIÓN DE ROLES - LÓGICA CORREGIDA
            $rolActual = $this->obtenerRolActual($id);
            $nuevoRol = $request->rol ?? $rolActual;
            
            // Solo procesar si el rol cambió o no tenía rol
            if ($nuevoRol !== $rolActual) {
                // Eliminar roles anteriores
                DB::table('medico')->where('usuario_id', $id)->delete();
                DB::table('paciente')->where('usuario_id', $id)->delete();
                DB::table('administrativo')->where('usuario_id', $id)->delete();
                
                // Crear nuevo rol
                switch ($nuevoRol) {
                    case 'medico':
                        DB::table('medico')->insert([
                            'usuario_id' => $id,
                            'especialidad' => $request->especialidad ?? 'General',
                            'nro_licencia' => $request->nro_licencia ?? 'LIC_' . $id,
                            'años_experiencia' => $request->años_experiencia ?? 0
                        ]);
                        break;
                        
                    case 'paciente':
                        DB::table('paciente')->insert([
                            'usuario_id' => $id,
                            'tipo_de_sangre' => $request->tipo_sangre ?? null
                        ]);
                        break;
                        
                    case 'administrativo':
                        DB::table('administrativo')->insert([
                            'usuario_id' => $id,
                            'cargo' => $request->cargo ?? 'Empleado',
                            'area_id' => $request->area_id ?? 1
                        ]);
                        break;
                }
            } else {
                // Actualizar datos del rol actual
                switch ($rolActual) {
                    case 'medico':
                        DB::table('medico')
                            ->where('usuario_id', $id)
                            ->update([
                                'especialidad' => $request->especialidad ?? 'General',
                                'nro_licencia' => $request->nro_licencia ?? 'LIC_' . $id,
                                'años_experiencia' => $request->años_experiencia ?? 0
                            ]);
                        break;
                        
                    case 'paciente':
                        DB::table('paciente')
                            ->where('usuario_id', $id)
                            ->update([
                                'tipo_de_sangre' => $request->tipo_sangre ?? null
                            ]);
                        break;
                        
                    case 'administrativo':
                        DB::table('administrativo')
                            ->where('usuario_id', $id)
                            ->update([
                                'cargo' => $request->cargo ?? 'Empleado',
                                'area_id' => $request->area_id ?? 1
                            ]);
                        break;
                }
            }

            // Registrar en auditoría
            DB::table('auditoria')->insert([
                'usuario_id' => session('usuario_id'),
                'accion' => 'Actualizó usuario ID: ' . $id,
                'fecha' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Usuario actualizado exitosamente',
                'rol_actual' => $nuevoRol
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar usuario: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error al actualizar usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    // Función auxiliar para obtener rol actual
    private function obtenerRolActual($usuarioId)
    {
        if (DB::table('medico')->where('usuario_id', $usuarioId)->exists()) {
            return 'medico';
        }
        if (DB::table('administrativo')->where('usuario_id', $usuarioId)->exists()) {
            return 'administrativo';
        }
        if (DB::table('paciente')->where('usuario_id', $usuarioId)->exists()) {
            return 'paciente';
        }
        return null;
    }

    // Dashboard mejorado - OPTIMIZADO Y CORREGIDO
    // En el método dashboardMejorado() corregir la redirección:
    
    public function dashboard()
    {
        if (!session('logged_in') || session('rol') !== 'administrativo') {
            return redirect()->route('ControlInicio.formlogin')
                ->with('error', 'Debe iniciar sesión como administrativo');
        }

        try {
            // Obtener estadísticas básicas
            $estadisticas = [
                'total_usuarios' => DB::table('usuario')->where('estado', 'activo')->count(),
                'total_pacientes' => DB::table('paciente')->count(),
                'total_medicos' => DB::table('medico')->count(),
                'total_administrativos' => DB::table('administrativo')->count(),
            ];

            // Obtener datos con paginación para mejor performance
            $usuarios = DB::table('usuario')
                ->where('estado', 'activo')
                ->orderBy('usuario_id', 'desc')
                ->limit(50)
                ->get();
            
            $pacientes = DB::table('paciente')
                ->join('usuario', 'paciente.usuario_id', '=', 'usuario.usuario_id')
                ->select(
                    'paciente.paciente_id',
                    'paciente.usuario_id',
                    'paciente.usuario_id',
                    'paciente.tipo_de_sangre',
                    'usuario.username',
                    'usuario.nombre',
                    'usuario.paterno',
                    'usuario.materno',
                    'usuario.correo',
                    'usuario.telefono',
                    'usuario.genero'
                )
                ->where('usuario.estado', 'activo')
                ->orderBy('paciente.paciente_id', 'desc')
                ->limit(20)
                ->get();
                
            $medicos = DB::table('medico')
                ->join('usuario', 'medico.usuario_id', '=', 'usuario.usuario_id')
                ->select(
                    'medico.medico_id',
                    'medico.usuario_id',
                    'medico.especialidad',
                    'medico.nro_licencia',
                    'usuario.telefono',
                    'usuario.username',
                    'usuario.nombre',
                    'usuario.paterno',
                    'usuario.materno',
                    'usuario.correo'
                )
                ->where('usuario.estado', 'activo')
                ->orderBy('medico.medico_id', 'desc')
                ->limit(20)
                ->get();
                
            $administrativos = DB::table('administrativo')
                ->join('usuario', 'administrativo.usuario_id', '=', 'usuario.usuario_id')
                ->leftJoin('area', 'administrativo.area_id', '=', 'area.area_id')
                ->select(
                    'administrativo.administrativo_id',
                    'administrativo.usuario_id',
                    'administrativo.cargo',
                    'area.nombre_area as area',
                    'usuario.username',
                    'usuario.nombre',
                    'usuario.paterno',
                    'usuario.materno',
                    'usuario.correo',
                    'usuario.telefono',
                    'area.nombre_area'
                )
                ->where('usuario.estado', 'activo')
                ->orderBy('administrativo.administrativo_id', 'desc')
                ->limit(20)
                ->get();
                
            $areas = DB::table('area')
                ->leftJoin('administrativo', 'area.area_id', '=', 'administrativo.area_id')
                ->select(
                    'area.area_id',
                    'area.nombre_area', 
                    'area.descripcion',
                    DB::raw('COUNT(administrativo.administrativo_id) as administradores_count')
                )
                ->groupBy('area.area_id', 'area.nombre_area', 'area.descripcion')
                ->orderBy('area.nombre_area')
                ->get();

            // Obtener últimas actividades de auditoría
            $ultimasActividades = DB::table('auditoria')
                ->join('usuario', 'auditoria.usuario_id', '=', 'usuario.usuario_id')
                ->select(
                    'auditoria.auditoria_id',
                    'auditoria.accion',
                    'auditoria.fecha',
                    'usuario.username',
                    DB::raw('CONCAT(usuario.nombre, " ", usuario.paterno) as usuario_nombre')
                )
                ->orderBy('auditoria.fecha', 'desc')
                ->limit(10)
                ->get();

            // Verificar que las funciones de la tarea están disponibles
            $funcionesDisponibles = [
                'fn_username_disponible' => $this->verificarFuncionExiste('fn_username_disponible'),
                'fn_obtener_info_administrador' => $this->verificarFuncionExiste('fn_obtener_info_administrador'),
                'vw_administradores_completo' => $this->verificarVistaExiste('vw_administradores_completo'),
                'sp_crear_administrador' => $this->verificarProcedimientoExiste('sp_crear_administrador')
            ];

            return view('pages.logins.administrador', compact(
                'usuarios',
                'pacientes', 
                'medicos',
                'administrativos',
                'areas',
                'estadisticas',
                'ultimasActividades',
                'funcionesDisponibles'
            ));

        } catch (\Exception $e) {
            Log::error('Error en dashboard mejorado: ' . $e->getMessage());
            return redirect()->route('ControlInicio.formlogin')
                ->with('error', 'Error al cargar el dashboard');
        }
    }

    // Funciones auxiliares para verificar objetos de BD
    private function verificarFuncionExiste($nombreFuncion)
    {
        try {
            $result = DB::select("
                SELECT 1 FROM information_schema.routines 
                WHERE routine_schema = DATABASE() 
                AND routine_name = ? 
                AND routine_type = 'FUNCTION'
            ", [$nombreFuncion]);
            
            return !empty($result);
        } catch (\Exception $e) {
            return false;
        }
    }
    
    private function verificarVistaExiste($nombreVista)
    {
        try {
            $result = DB::select("
                SELECT 1 FROM information_schema.views 
                WHERE table_schema = DATABASE() 
                AND table_name = ?
            ", [$nombreVista]);
            
            return !empty($result);
        } catch (\Exception $e) {
            return false;
        }
    }
    
    private function verificarProcedimientoExiste($nombreProcedimiento)
    {
        try {
            $result = DB::select("
                SELECT 1 FROM information_schema.routines 
                WHERE routine_schema = DATABASE() 
                AND routine_name = ? 
                AND routine_type = 'PROCEDURE'
            ", [$nombreProcedimiento]);
            
            return !empty($result);
        } catch (\Exception $e) {
            return false;
        }
    }
}