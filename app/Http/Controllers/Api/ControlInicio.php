<?php
namespace App\Http\Controllers\Api;
use App\Http\Requests;               //Requests
use App\Http\Controllers\Controller; //Herencia de controlador
use Illuminate\Support\Facades\DB; //Base de datos
use Illuminate\Http\Request;       //Request pero que es illuminate? Respuesta: una libreria que contiene request
use Illuminate\Http\JsonResponse; //para Json response
use Illuminate\Support\Facades\Hash; // para verificar contraseñas
class ControlInicio extends Controller
{
    public function index()
    {
        return view('pages.inicio');
    }
    public function main()
    {
        return view('pages.main');
    }
    public function formlogin()
    {
        return view('pages.login');
    }
    public function login2(Request $request)
    {
        // TEMPORAL: Redirigir directamente sin validar
        session([
            'usuario_id' => 1,
            'username'   => 'admin_test',
            'rol'        => 'administrativo',
            'logged_in'  => true
        ]);
        
        return redirect()->route('administrador.dashboard');
    }
    public function login(Request $request)
    {
        // Validación de datos
        $request->validate([
            'rol' => 'required',
            'username' => 'required',
            'password' => 'required'
        ]);

        // Buscar usuario por username
        $usuario = DB::table('usuario')
            ->where('username', $request->username)
            ->first();

        // DEBUG 1: Verificar si encontró usuario
        if (!$usuario) {
            \Log::info('Usuario no encontrado: ' . $request->username);
            return back()->with('error', 'Usuario no encontrado');
        }

        // Validar password
        if (hash('sha256', $request->password) !== $usuario->contraseña) {
            \Log::info('Contraseña incorrecta para usuario: ' . $request->username);
            return back()->with('error', 'Contraseña incorrecta');
        }

        // Validar rol según tu base de datos
        $rol = $request->rol;
        $rolValido = false;

        switch ($rol) {
            case 'medico':
                $rolValido = DB::table('medico')->where('usuario_id', $usuario->usuario_id)->exists();
                break;
            case 'paciente':
                $rolValido = DB::table('paciente')->where('usuario_id', $usuario->usuario_id)->exists();
                break;
            case 'administrativo':
                $rolValido = DB::table('administrativo')->where('usuario_id', $usuario->usuario_id)->exists();
                break;
            default:
                $rolValido = false;
                break;
        }

        // DEBUG 2: Verificar rol
        if (!$rolValido) {
            \Log::info('Rol no válido. Usuario: ' . $usuario->usuario_id . ', Rol solicitado: ' . $rol);
            return back()->with('error', 'El usuario no pertenece al rol seleccionado.');
        }

        // Guardar datos en sesión
        session([
            'usuario_id' => $usuario->usuario_id,
            'username'   => $usuario->username,
            'nombre'     => $usuario->nombre,
            'paterno'    => $usuario->paterno,
            'materno'    => $usuario->materno,
            'correo'     => $usuario->correo,
            'telefono'   => $usuario->telefono,
            'rol'        => $rol,
            'logged_in'  => true
        ]);

        // DEBUG 3: Verificar sesión
        \Log::info('Sesión guardada:', session()->all());

        // Redirecciones por rol
        if ($rol === 'medico') {
            \Log::info('Redirigiendo a médico');
            return redirect()->route('medico.dashboard');
        }

        if ($rol === 'paciente') {
            \Log::info('Redirigiendo a paciente');
            return redirect()->route('paciente.dashboard');
        }

        if ($rol === 'administrativo') {
            \Log::info('Redirigiendo a administrador');
            return redirect()->route('administrador.dashboard');
        }
    }
    public function logout(Request $request)
    {
        session()->flush();
        return redirect()->route('ControlInicio.formlogin')->with('success', 'Sesión cerrada correctamente');
    }
}
