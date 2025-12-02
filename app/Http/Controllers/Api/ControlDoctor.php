<?php
namespace App\Http\Controllers\Api;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Doctor;

class ControlDoctor extends Controller
{
    // 📋 LISTA DE DOCTORES
    public function index()
    {
        // ✅ Usar el método del modelo con cifrado
        $doctores = Doctor::obtenerDoctores();
        
        return view('pages.doctores.index', compact('doctores'));
    }

    // ➕ FORMULARIO CREAR DOCTOR
    public function create()
    {
        $especialidades = DB::select('SELECT DISTINCT especialidad FROM medico WHERE especialidad IS NOT NULL');
        return view('pages.doctores.create', compact('especialidades'));
    }

    // 💾 GUARDAR DOCTOR
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:usuario,username',
            'nombre' => 'required',
            'paterno' => 'required',
            'correo' => 'required|email|unique:usuario,correo_original',
            'contraseña' => 'required|min:6',
            'especialidad' => 'required',
            'nro_licencia' => 'required',
            'años_experiencia' => 'required|integer|min:0'
        ]);

        try {
            $result = Doctor::crearDoctor($request->all());
            return redirect()->route('doctores.index')
                ->with('success', 'Doctor creado exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al crear doctor: ' . $e->getMessage());
        }
    }
     // 📋 VALIDAR LICENCIA (AJAX)
public function validarLicencia(Request $request)
{
    $request->validate([
        'nro_licencia' => 'required|string|min:3'
    ]);

    $nroLicencia = $request->input('nro_licencia');
    
    try {
        // Verificar si la licencia ya existe
        $existe = DB::select('
            SELECT COUNT(*) as total 
            FROM medico 
            WHERE nro_licencia = ?
        ', [$nroLicencia]);
        
        $licenciaExiste = $existe[0]->total > 0;
        
        if ($licenciaExiste) {
            return response()->json([
                'error' => true,
                'message' => '❌ Esta licencia ya está registrada en el sistema'
            ]);
        } else {
            return response()->json([
                'error' => false,
                'message' => '✅ Licencia disponible para uso'
            ]);
        }
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => true,
            'message' => 'Error al validar licencia: ' . $e->getMessage()
        ], 500);
    }
}

    // 👁️ VISTA DETALLADA DE DOCTOR
    public function show($id)
    {
        $doctor = Doctor::obtenerDoctor($id);
        
        if (empty($doctor)) {
            return redirect()->route('doctores.index')
                ->with('error', 'Doctor no encontrado');
        }

        return view('pages.doctores.show', ['doctor' => $doctor[0]]);
    }

    // ✏️ FORMULARIO EDITAR DOCTOR
    public function edit($id)
    {
        $doctor = DB::select('
            SELECT m.*, u.* 
            FROM medico m 
            JOIN usuario u ON m.usuario_id = u.usuario_id 
            WHERE m.medico_id = ?
        ', [$id]);

        if (empty($doctor)) {
            return redirect()->route('doctores.index')
                ->with('error', 'Doctor no encontrado');
        }

        $especialidades = DB::select('SELECT DISTINCT especialidad FROM medico WHERE especialidad IS NOT NULL');
        
        return view('pages.doctores.edit', [
            'doctor' => $doctor[0],
            'especialidades' => $especialidades
        ]);
    }

    // 📤 ACTUALIZAR DOCTOR
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required',
            'paterno' => 'required',
            'correo' => 'required|email',
            'especialidad' => 'required',
            'nro_licencia' => 'required',
            'años_experiencia' => 'required|integer|min:0'
        ]);

        try {
            $result = Doctor::actualizarDoctor($id, $request->all());
            return redirect()->route('doctores.index')
                ->with('success', 'Doctor actualizado exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar doctor: ' . $e->getMessage());
        }
    }

    // 🗑️ ELIMINAR DOCTOR
    public function destroy($id)
    {
        try {
            DB::delete('DELETE FROM medico WHERE medico_id = ?', [$id]);
            return redirect()->route('doctores.index')
                ->with('success', 'Doctor eliminado exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar doctor: ' . $e->getMessage());
        }
    }

    // 📊 REPORTE DE ESPECIALIDADES
    public function reporteEspecialidades()
    {
        try {
            $reporte = DB::select('
                SELECT 
                    especialidad,
                    COUNT(*) as total_doctores,
                    AVG(años_experiencia) as experiencia_promedio,
                    MIN(años_experiencia) as exp_minima,
                    MAX(años_experiencia) as exp_maxima
                FROM medico
                WHERE especialidad IS NOT NULL
                GROUP BY especialidad
            ');
            return view('pages.doctores.reporte-especialidades', compact('reporte'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error en reporte: ' . $e->getMessage());
        }
    }

    // 📈 ESTADÍSTICAS
    public function estadisticas()
    {
        try {
            $resultados = DB::select('
                SELECT 
                    especialidad,
                    COUNT(*) as total_doctores
                FROM medico 
                WHERE especialidad IS NOT NULL 
                GROUP BY especialidad
                ORDER BY total_doctores DESC
            ');

            if (empty($resultados)) {
                return view('pages.doctores.estadisticas', [
                    'stats' => [],
                    'totalDoctores' => 0,
                    'totalEspecialidades' => 0,
                    'mensaje' => 'No hay doctores registrados en el sistema'
                ]);
            }

            $stats = [];
            foreach ($resultados as $result) {
                $stats[$result->especialidad] = $result->total_doctores;
            }

            $totalDoctores = array_sum($stats);
            $totalEspecialidades = count($stats);

            return view('pages.doctores.estadisticas', [
                'stats' => $stats,
                'totalDoctores' => $totalDoctores,
                'totalEspecialidades' => $totalEspecialidades
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error en estadísticas: ' . $e->getMessage());
            
            return view('pages.doctores.estadisticas', [
                'stats' => [],
                'totalDoctores' => 0,
                'totalEspecialidades' => 0,
                'error' => 'Error al cargar estadísticas. Verifica que existan doctores en la base de datos.'
            ]);
        }
    }

    // 🔓 FUNCIÓN PARA DESENCRIPTAR DATOS (SOLO DESARROLLO)
    public function datosReales($id)
    {
        if (!env('APP_DEBUG')) {
            abort(403, 'Acceso solo en modo desarrollo');
        }

        $doctor = Doctor::obtenerDoctor($id);
        
        if (empty($doctor)) {
            return response()->json(['error' => 'Doctor no encontrado'], 404);
        }

        $datos = $doctor[0];
        
        return response()->json([
            'correo_real' => Doctor::descifrarCorreo($datos->correo_cifrado),
            'telefono_real' => Doctor::descifrarTelefono($datos->telefono_cifrado)
        ]);
    }
   
}