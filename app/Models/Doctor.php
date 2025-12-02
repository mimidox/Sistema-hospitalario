<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Doctor extends Model
{
    protected $table = 'medico';
    protected $primaryKey = 'medico_id';
    public $timestamps = false;

    protected $fillable = [
        'usuario_id', 'especialidad', 'nro_licencia', 'años_experiencia'
    ];

    // Relación con usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    // Usar procedimiento almacenado CON cifrado
    public static function crearDoctor($data)
    {
        return DB::select('CALL sp_crear_doctor_cifrado(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
            $data['username'], $data['nombre'], $data['paterno'],
            $data['materno'], $data['genero'], $data['correo'],
            $data['contraseña'], $data['telefono'],
            $data['especialidad'], $data['nro_licencia'], $data['años_experiencia']
        ]);
    }

    public static function actualizarDoctor($id, $data)
    {
        return DB::select('CALL sp_actualizar_doctor(?, ?, ?, ?, ?, ?, ?, ?, ?)', [
            $id, $data['nombre'], $data['paterno'], $data['materno'],
            $data['correo'], $data['telefono'], $data['especialidad'],
            $data['nro_licencia'], $data['años_experiencia']
        ]);
    }

    // ✅ CONSULTA CON CIFRADO - CORREGIDA
    public static function obtenerDoctores()
    {
        return DB::select('
            SELECT 
                m.medico_id,
                CONCAT(u.nombre, " ", u.paterno, " ", COALESCE(u.materno, "")) as nombre_completo,
                HEX(u.correo_cifrado) as correo_cifrado,
                HEX(u.telefono_cifrado) as telefono_cifrado,
                u.contraseña,
                m.especialidad,
                m.nro_licencia,
                m.años_experiencia,
                (SELECT COUNT(*) FROM consulta c WHERE c.medico_id = m.medico_id) as total_consultas
            FROM medico m
            JOIN usuario u ON m.usuario_id = u.usuario_id
            ORDER BY m.medico_id DESC
        ');
    }

    // ✅ CONSULTA CON CIFRADO - DETALLE
    public static function obtenerDoctor($id)
    {
        return DB::select('
            SELECT 
                m.medico_id,
                CONCAT(u.nombre, " ", u.paterno, " ", COALESCE(u.materno, "")) as nombre_completo,
                HEX(u.correo_cifrado) as correo_cifrado,
                HEX(u.telefono_cifrado) as telefono_cifrado,
                u.contraseña,
                m.especialidad,
                m.nro_licencia,
                m.años_experiencia,
                (SELECT COUNT(*) FROM consulta c WHERE c.medico_id = m.medico_id) as total_consultas
            FROM medico m
            JOIN usuario u ON m.usuario_id = u.usuario_id
            WHERE m.medico_id = ?
        ', [$id]);
    }

    // 🔓 FUNCIONES PARA DESENCRIPTAR
    public static function descifrarTelefono($telefonoCifradoHex)
    {
        if (empty($telefonoCifradoHex)) return null;
        
        $result = DB::select('
            SELECT AES_DECRYPT(UNHEX(?), "clave_secreta_medilab_2025") as telefono
        ', [$telefonoCifradoHex]);
        
        return $result[0]->telefono ?? null;
    }

    public static function descifrarCorreo($correoCifradoHex)
    {
        if (empty($correoCifradoHex)) return null;
        
        $result = DB::select('
            SELECT AES_DECRYPT(UNHEX(?), "clave_correo_medilab_2025") as correo
        ', [$correoCifradoHex]);
        
        return $result[0]->correo ?? null;
    }

    // Usar función almacenada
    public static function contarPorEspecialidad($especialidad)
    {
        $result = DB::select('SELECT fn_contar_doctores_especialidad(?) as total', [$especialidad]);
        return $result[0]->total ?? 0;
    }

    // Obtener especialidades únicas
    public static function obtenerEspecialidades()
    {
        return DB::select('SELECT DISTINCT especialidad FROM medico WHERE especialidad IS NOT NULL');
    }
}