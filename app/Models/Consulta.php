<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consulta extends Model
{
    protected $table = 'consulta';
    protected $primaryKey = 'consulta_id';
    public $timestamps = false;

    protected $fillable = [
        'medico_id',
        'ficha_id',
        'motivo_consulta',
        'fecha',
        'tipo',
    ];

    public function medico()
    {
        return $this->belongsTo(\App\Models\Medico::class, 'medico_id', 'medico_id');
    }

    public function paciente()
    {
        // Si ficha_id apunta a paciente_id directamente:
        return $this->belongsTo(\App\Models\Paciente::class, 'ficha_id', 'paciente_id');
    }
}