<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hospitalizacion extends Model
{
    use HasFactory;

    protected $table = 'hospitalizacion';
    protected $primaryKey = 'hospitalizacion_id';
    public $timestamps = false;

    protected $fillable = [
        'paciente_id',
        'medico_id',
        'habitacion_id',
        'fecha_ingreso',
        'fecha_alta',
        'diagnostico'
    ];

    /**
     * Relación con Paciente
     */
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id', 'paciente_id');
    }

    /**
     * Relación con Medico
     */
    public function medico()
    {
        return $this->belongsTo(Medico::class, 'medico_id', 'medico_id');
    }

    /**
     * Relación con Habitacion
     */
    public function habitacion()
    {
        return $this->belongsTo(Habitacion::class, 'habitacion_id', 'habitacion_id');
    }
}