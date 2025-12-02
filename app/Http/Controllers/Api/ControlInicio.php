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

        public function login(Request $request)
    {
        try {
            // Validación de datos con mensajes personalizados
            $validated = $request->validate([
                'rol' => 'required|in:medico,paciente,administrativo',
                'username' => 'required|string|max:50',
                'password' => 'required|string|min:3'
            ], [
                'rol.required' => 'Debe seleccionar un rol.',
                'rol.in' => 'El rol seleccionado no es válido.',
                'username.required' => 'El usuario es obligatorio.',
                'password.required' => 'La contraseña es obligatoria.',
                'password.min' => 'La contraseña debe tener al menos :min caracteres.'
            ]);

            // Buscar usuario por username
            $usuario = DB::table('usuario')
                ->where('username', $request->username)
                ->first();

            // Verificar si el usuario existe
            if (!$usuario) {
                \Log::warning('Intento de login fallido - Usuario no encontrado: ' . $request->username);
                return back()
                    ->withInput($request->only('username', 'rol'))
                    ->with('error', 'Usuario no encontrado. Verifique su nombre de usuario.');
            }

            // Verificar si el usuario está activo
            if ($usuario->estado !== 'activo') {
                \Log::warning('Intento de login fallido - Usuario inactivo: ' . $request->username);
                return back()
                    ->withInput($request->only('username', 'rol'))
                    ->with('error', 'Su cuenta está inactiva. Contacte al administrador.');
            }

            // Validar password
            $hashedPassword = hash('sha256', $request->password);
            if ($hashedPassword !== $usuario->contraseña) {
                \Log::warning('Intento de login fallido - Contraseña incorrecta para usuario: ' . $request->username);
                
                // Incrementar intentos fallidos (opcional, para implementar bloqueo)
                // $this->incrementFailedAttempts($usuario->usuario_id);
                
                return back()
                    ->withInput($request->only('username', 'rol'))
                    ->with('error', 'Contraseña incorrecta. Intente nuevamente.');
            }

            // Validar rol según tu base de datos
            $rol = $request->rol;
            $rolValido = false;
            $rolTable = '';
            $rolName = '';

            switch ($rol) {
                case 'medico':
                    $rolValido = DB::table('medico')->where('usuario_id', $usuario->usuario_id)->exists();
                    $rolTable = 'medico';
                    $rolName = 'Médico';
                    break;
                case 'paciente':
                    $rolValido = DB::table('paciente')->where('usuario_id', $usuario->usuario_id)->exists();
                    $rolTable = 'paciente';
                    $rolName = 'Paciente';
                    break;
                case 'administrativo':
                    $rolValido = DB::table('administrativo')->where('usuario_id', $usuario->usuario_id)->exists();
                    $rolTable = 'administrativo';
                    $rolName = 'Administrativo';
                    break;
            }

            // Verificar rol
            if (!$rolValido) {
                \Log::warning('Intento de login fallido - Rol no válido. Usuario: ' . $usuario->usuario_id . ', Rol solicitado: ' . $rol);
                
                // Obtener el rol real del usuario
                $actualRol = $this->getUserActualRole($usuario->usuario_id);
                $errorMessage = $actualRol 
                    ? "Este usuario pertenece al rol de {$actualRol}. Seleccione el rol correcto."
                    : "El usuario no tiene un rol asignado en el sistema.";
                
                return back()
                    ->withInput($request->only('username', 'rol'))
                    ->with('error', $errorMessage);
            }

            // Obtener información adicional según el rol
            $additionalInfo = [];
            if ($rolTable) {
                $additionalInfo = DB::table($rolTable)
                    ->where('usuario_id', $usuario->usuario_id)
                    ->first() ?? [];
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
                'rol_name'   => $rolName,
                'additional_info' => $additionalInfo,
                'logged_in'  => true,
                'login_time' => now()
            ]);

            // Registrar login exitoso en auditoría
            DB::table('auditoria')->insert([
                'usuario_id' => $usuario->usuario_id,
                'accion' => 'Login exitoso - ' . $rolName,
                'fecha' => now()
            ]);

            \Log::info('Login exitoso - Usuario: ' . $usuario->username . ', Rol: ' . $rol);

            // Redirecciones por rol
            $redirectRoutes = [
                'medico' => 'medico.dashboard',
                'paciente' => 'paciente.dashboard',
                'administrativo' => 'administrador.dashboard'
            ];

            if (isset($redirectRoutes[$rol])) {
                return redirect()->route($redirectRoutes[$rol])
                    ->with('success', '¡Bienvenido ' . $usuario->nombre . '!');
            }

            return redirect()->route('home')
                ->with('success', '¡Bienvenido ' . $usuario->nombre . '!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Capturar errores de validación
            \Log::warning('Error de validación en login: ' . $e->getMessage());
            throw $e;
            
        } catch (\Exception $e) {
            // Capturar cualquier otro error
            \Log::error('Error en login: ' . $e->getMessage());
            return back()
                ->withInput($request->only('username', 'rol'))
                ->with('error', 'Ocurrió un error inesperado. Por favor, intente nuevamente.');
        }
    }

    // Función auxiliar para obtener el rol real del usuario
    private function getUserActualRole($usuarioId)
    {
        if (DB::table('medico')->where('usuario_id', $usuarioId)->exists()) {
            return 'Médico';
        }
        if (DB::table('administrativo')->where('usuario_id', $usuarioId)->exists()) {
            return 'Administrativo';
        }
        if (DB::table('paciente')->where('usuario_id', $usuarioId)->exists()) {
            return 'Paciente';
        }
        return null;
    }

    // Función para incrementar intentos fallidos (opcional)
    private function incrementFailedAttempts($usuarioId)
    {
        // Puedes implementar lógica para bloquear usuarios después de X intentos
        // Ejemplo: guardar en una tabla 'login_attempts'
        DB::table('login_attempts')->updateOrInsert(
            ['usuario_id' => $usuarioId],
            [
                'attempts' => DB::raw('attempts + 1'),
                'last_attempt' => now(),
                'updated_at' => now()
            ]
        );
    }

    public function logout(Request $request)
    {
        if (session('usuario_id')) {
            // Registrar logout en auditoría
            DB::table('auditoria')->insert([
                'usuario_id' => session('usuario_id'),
                'accion' => 'Logout - ' . session('rol_name'),
                'fecha' => now()
            ]);
            
            \Log::info('Logout exitoso - Usuario: ' . session('username'));
        }
        
        session()->flush();
        return redirect()->route('ControlInicio.formlogin')
            ->with('success', 'Sesión cerrada correctamente. ¡Vuelva pronto!');
    }

}
