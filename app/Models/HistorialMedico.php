<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialMedico extends Model
{
    use HasFactory;

    protected $table = 'historial_medico';
    protected $primaryKey = 'historial_id';
    public $timestamps = false;

    protected $fillable = [
        'paciente_id',
        'antecedentes',
        'alergias',
        'diagnosticos'
    ];

    /**
     * Relación con Paciente
     */
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id', 'paciente_id');
    }

    /**
     * Relación con Tratamientos
     */
    public function tratamientos()
    {
        return $this->hasMany(Tratamiento::class, 'historial_id', 'historial_id');
    }
}