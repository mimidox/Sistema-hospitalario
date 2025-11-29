<?php
namespace App\Http\Controllers\Api;
use App\Http\Requests;               //Requests
use App\Http\Controllers\Controller; //Herencia de controlador
use Illuminate\Support\Facades\DB; //Base de datos
use Illuminate\Http\Request;       //Request pero que es illuminate? Respuesta: una libreria que contiene request
use Illuminate\Http\JsonResponse; //para Json response
class ControlAdmin extends Controller
{
    public function crearUsuario(Request $request)
    {
        if (!session('logged_in') || session('rol') !== 'administrativo') {
            return redirect()->route('ControlInicio.formlogin');
        }

        try {
            DB::table('usuario')->insert([
                'username' => $request->username,
                'nombre' => $request->nombre,
                'paterno' => $request->paterno,
                'materno' => $request->materno,
                'genero' => $request->genero,
                'correo' => $request->correo,
                'contraseña' => $request->contraseña,
                'telefono' => $request->telefono,
                'calle' => $request->calle,
                'zona' => $request->zona,
                'municipio' => $request->municipio,
                'fec_nac' => $request->fec_nac,
            ]);

            return back()->with('success', 'Usuario creado exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al crear usuario: ' . $e->getMessage());
        }
    }

    // Método para eliminar usuario
    public function eliminarUsuario($id)
    {
        if (!session('logged_in') || session('rol') !== 'administrativo') {
            return redirect()->route('ControlInicio.formlogin');
        }

        try {
            DB::table('usuario')->where('usuario_id', $id)->delete();
            return back()->with('success', 'Usuario eliminado exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar usuario: ' . $e->getMessage());
        }
    }

    public function formulario()
    {
        // Obtener todas las zonas disponibles para el select
        $Zonas = DB::table('zona')
            // ->select('codigo_zona', 'nombre_zona')
            ->get();

        return view('pages.formularioBarrio', compact('Zonas'));
    }

    public function adicionar(Request $request)
    {
        $adicionar = DB::insert('insert into Barrio values(?,?,?)', [$request ->codigo_barrio,$request ->nombre_barrio,$request->codigo_zona]);
        if($adicionar == true)
        	return redirect('/listadoBarrios')->with('success', 'Barrio adicionada correctamente');
		else
			return redirect('/listadoBarrios')->with('error', 'Error al adicionar Barrio');
    }

    public function eliminar($codigo_barrio)
    {
        $flag = False;
        $barrio = DB::select('select nombre_barrio from barrio where codigo_barrio = ?', [$codigo_barrio]);
        $countCalles = DB::table('Calle')->where('codigo_barrio', $codigo_barrio)->count();
        if ($countCalles == 0) {
            $eliminado = DB::delete('delete from barrio where codigo_barrio = ?', [$codigo_barrio]);
            if($eliminado)
                $flag = True;
        }
        else{
            $eliminadoCalles =DB::delete('delete from calle where codigo_barrio = ?',[$codigo_barrio]);
            $eliminado = DB::delete('delete from barrio where codigo_barrio = ?', [$codigo_barrio]);
            if($eliminado && $eliminadoCalles)
                $flag = True;
        }
        $nombre_barrio = $barrio[0]->nombre_barrio;
        if($flag)
            return redirect('/listadoBarrios')->with('success', "Barrio '{$nombre_barrio}' ha sido eliminada correctamente");
        else
            return redirect('/listadoBarrios')->with('error', "No se pudo eliminar la Barrio");
    }

    public function editar($codigo_barrio)
    {
        // Buscamos el barrio
        $barrio = DB::table('barrio')->where('codigo_barrio', $codigo_barrio)->first();

        // Si no existe, redirigimos con error
        if (!$barrio) {
            return redirect()->route('ControlBarrio.lista')->with('error', 'Barrio no encontrado');
        }

        // Obtenemos todas las zonas para el select
        $zonas = DB::table('zona')
            ->select('codigo_zona', 'nombre_zona')
            ->get();

        // Enviamos el objeto barrio y las zonas a la vista
        return view('pages.editarBarrio', [
            'Barrio' => $barrio,
            'Zonas' => $zonas
        ]);
    }

    public function update(Request $request, $codigo_barrio)
    {
        $update = DB::update('update Barrio set nombre_barrio = ?, codigo_zona = ? where codigo_barrio = ?',
        [$request->nombre_barrio, $request->codigo_zona, $codigo_barrio]);
        if($update == true)
            return redirect('/listadoBarrios')->with('success','Barrio editado correctamente');
        else
            return redirect('/listadoBarrios')->with('error','Error al editar Barrio');
    }
}
