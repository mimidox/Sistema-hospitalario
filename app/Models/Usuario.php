<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;

    protected $table = 'usuario';
    protected $primaryKey = 'usuario_id';
    public $timestamps = false;

    protected $fillable = [
        'username',
        'nombre',
        'paterno',
        'materno',
        'genero',
        'correo',
        'contraseña',
        'telefono',
        'calle',
        'zona',
        'municipio',
        'fec_nac'
    ];

    protected $hidden = [
        'contraseña'
    ];

    /**
     * Relación con Paciente
     */
    public function paciente()
    {
        return $this->hasOne(Paciente::class, 'usuario_id', 'usuario_id');
    }

    /**
     * Relación con Medico
     */
    public function medico()
    {
        return $this->hasOne(Medico::class, 'usuario_id', 'usuario_id');
    }

    /**
     * Relación con Administrativo
     */
    public function administrativo()
    {
        return $this->hasOne(Administrativo::class, 'usuario_id', 'usuario_id');
    }
}