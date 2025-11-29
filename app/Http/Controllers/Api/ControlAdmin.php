<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ControlAdmin extends Controller
{
    // Obtener datos completos de un usuario con sus roles
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
            $usuario = DB::table('usuario')->where('usuario_id', $id)->first();
            
            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            // Verificar roles del usuario
            $roles = [];

            // Verificar si es médico
            $medico = DB::table('medico')->where('usuario_id', $id)->first();
            if ($medico) {
                $roles['medico'] = $medico;
            }

            // Verificar si es paciente
            $paciente = DB::table('paciente')->where('usuario_id', $id)->first();
            if ($paciente) {
                $roles['paciente'] = $paciente;
            }

            // Verificar si es administrativo
            $administrativo = DB::table('administrativo')->where('usuario_id', $id)->first();
            if ($administrativo) {
                $roles['administrativo'] = $administrativo;
            }

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

    // Crear usuario - CORREGIDO
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
            $request->validate([
                'username' => 'required|unique:usuario',
                'nombre' => 'required',
                'paterno' => 'required',
                'correo' => 'required|email|unique:usuario',
                'password' => 'required|min:6'
            ]);

            $usuarioId = DB::table('usuario')->insertGetId([
                'username' => $request->username,
                'nombre' => $request->nombre,
                'paterno' => $request->paterno,
                'materno' => $request->materno ?? null,
                'genero' => $request->genero ?? 'M',
                'correo' => $request->correo,
                'contraseña' => $request->password, // Cambiado de 'contraseña' a 'password'
                'telefono' => $request->telefono ?? null,
                'calle' => $request->calle ?? null,
                'zona' => $request->zona ?? null,
                'municipio' => $request->municipio ?? null,
                'fec_nac' => $request->fec_nac ?? null,
            ]);
            $pacienteId = DB::table('paciente')->insertGetId([
                'usuario_id' => $usuarioId,
                'num_historial' => $request->num_historial ?? null,
                'tipo_sangre' => $request->tipo_sangre ?? null,
            ]);
            return response()->json([
                'success' => true, 
                'message' => 'Usuario creado exitosamente',
                'usuario_id' => $usuarioId
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Error de validación: ' . $e->getMessage(),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al crear usuario: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error al crear usuario'
            ], 500);
        }
    }

    // Actualizar usuario - CON GESTIÓN DE ROLES
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
            $usuario = DB::table('usuario')->where('usuario_id', $id)->first();
            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            // Validar campos únicos excluyendo el usuario actual
            $request->validate([
                'username' => 'required|unique:usuario,username,' . $id . ',usuario_id',
                'correo' => 'required|email|unique:usuario,correo,' . $id . ',usuario_id',
                'nombre' => 'required',
                'paterno' => 'required'
            ]);

            // Iniciar transacción para asegurar consistencia
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
            ];

            // Si se proporciona una nueva contraseña, cifrarla con SHA2
            if ($request->filled('password') && !empty($request->password)) {
                $datosActualizar['contraseña'] = hash('sha256', $request->password);
            }

            $actualizado = DB::table('usuario')->where('usuario_id', $id)->update($datosActualizar);

            // GESTIÓN DE ROLES
            if ($actualizado) {
                $rol = $request->rol ?? null;
                
                // Verificar roles actuales del usuario
                $esPaciente = DB::table('paciente')->where('usuario_id', $id)->exists();
                $esMedico = DB::table('medico')->where('usuario_id', $id)->exists();
                $esAdministrativo = DB::table('administrativo')->where('usuario_id', $id)->exists();

                // Manejar rol de MÉDICO
                if ($rol === 'medico') {
                    if (!$esMedico) {
                        // Crear registro de médico si no existe
                        DB::table('medico')->insert([
                            'usuario_id' => $id,
                            'especialidad' => $request->especialidad ?? 'General',
                            'nro_licencia' => $request->nro_licencia ?? null,
                            'años_experiencia' => $request->años_experiencia ?? null,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    } else {
                        // Actualizar médico existente
                        DB::table('medico')->where('usuario_id', $id)->update([
                            'especialidad' => $request->especialidad ?? 'General',
                            'nro_licencia' => $request->nro_licencia ?? null,
                            'años_experiencia' => $request->años_experiencia ?? null,
                            'updated_at' => now()
                        ]);
                    }
                    
                    // Remover rol de paciente si existía
                    if ($esPaciente) {
                        DB::table('paciente')->where('usuario_id', $id)->delete();
                    }
                }
                // Manejar rol de ADMINISTRATIVO
                elseif ($rol === 'administrativo') {
                    if (!$esAdministrativo) {
                        // Crear registro de administrativo si no existe
                        DB::table('administrativo')->insert([
                            'usuario_id' => $id,
                            'cargo' => $request->cargo ?? 'Empleado',
                            'area_id' => $request->area_id ?? "1",
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    } else {
                        // Actualizar administrativo existente
                        DB::table('administrativo')->where('usuario_id', $id)->update([
                            'cargo' => $request->cargo ?? 'Empleado',
                            'area_id' => $request->area_id ?? "1",
                            'updated_at' => now()
                        ]);
                    }
                    
                    // Remover rol de paciente si existía
                    if ($esPaciente) {
                        DB::table('paciente')->where('usuario_id', $id)->delete();
                    }
                }
                // Manejar rol de PACIENTE (por defecto)
                elseif ($rol === 'paciente' || $rol === null) {
                    if (!$esPaciente) {
                        // Crear registro de paciente si no existe
                        DB::table('paciente')->insert([
                            'usuario_id' => $id,
                            'tipo_de_sangre' => $request->tipo_sangre ?? null,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    } else {
                        // Actualizar paciente existente
                        DB::table('paciente')->where('usuario_id', $id)->update([
                            'tipo_de_sangre' => $request->tipo_sangre ?? null,
                            'updated_at' => now()
                        ]);
                    }
                    
                    // Remover otros roles si existían
                    if ($esMedico) {
                        DB::table('medico')->where('usuario_id', $id)->delete();
                    }
                    if ($esAdministrativo) {
                        DB::table('administrativo')->where('usuario_id', $id)->delete();
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Usuario y roles actualizados exitosamente'
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
            Log::error('Error al actualizar usuario: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error al actualizar usuario: ' . $e->getMessage()
            ], 500);
        }
    }
    public function actualizarUsuario2(Request $request, $id)
    {
        if (!session('logged_in') || session('rol') !== 'administrativo') {
            return response()->json([
                'success' => false, 
                'message' => 'No autorizado'
            ], 401);
        }

        try {
            // Validar que el usuario existe
            $usuario = DB::table('usuario')->where('usuario_id', $id)->first();
            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            // Validar campos únicos excluyendo el usuario actual
            $request->validate([
                'username' => 'required|unique:usuario,username,' . $id . ',usuario_id',
                'correo' => 'required|email|unique:usuario,correo,' . $id . ',usuario_id',
                'nombre' => 'required',
                'paterno' => 'required'
            ]);

            // Preparar datos para actualizar
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
            ];

            // Si se proporciona una nueva contraseña, cifrarla con SHA2
            if ($request->filled('password')) {
                $datosActualizar['contraseña'] = hash('sha256', $request->password);
            }

            $actualizado = DB::table('usuario')->where('usuario_id', $id)->update($datosActualizar);

            if ($actualizado) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Usuario actualizado exitosamente'
                ]);
            } else {
                return response()->json([
                    'success' => false, 
                    'message' => 'No se realizaron cambios en el usuario'
                ]);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al actualizar usuario: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error al actualizar usuario'
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

    // Método para el dashboard del administrador - OPTIMIZADO
    public function dashboard()
    {
        if (!session('logged_in') || session('rol') !== 'administrativo') {
            return redirect()->route('ControlInicio.formlogin')
                ->with('error', 'Debe iniciar sesión como administrativo');
        }

        try {
            // Obtener datos para todas las tablas
            $usuarios = DB::table('usuario')->get();
            
            $pacientes = DB::table('paciente')
                ->join('usuario', 'paciente.usuario_id', '=', 'usuario.usuario_id')
                ->select('paciente.*', 'usuario.username', 'usuario.nombre', 'usuario.paterno', 'usuario.materno', 
                        'usuario.correo', 'usuario.telefono', 'usuario.genero', 'usuario.fec_nac')
                ->get();
                
            $medicos = DB::table('medico')
                ->join('usuario', 'medico.usuario_id', '=', 'usuario.usuario_id')
                ->select('medico.*', 'usuario.username', 'usuario.nombre', 'usuario.paterno', 'usuario.materno', 
                        'usuario.correo', 'usuario.telefono', 'usuario.genero')
                ->get();
                
            $administrativos = DB::table('administrativo')
                ->join('usuario', 'administrativo.usuario_id', '=', 'usuario.usuario_id')
                ->select('administrativo.*', 'usuario.username', 'usuario.nombre', 'usuario.paterno', 'usuario.materno', 
                        'usuario.correo', 'usuario.telefono')
                ->get();

            return view('pages.logins.administrador', compact('usuarios', 'pacientes', 'medicos', 'administrativos'));

        } catch (\Exception $e) {
            Log::error('Error en dashboard admin: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar el dashboard');
        }
    }
}