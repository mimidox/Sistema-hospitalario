<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consulta extends Model
{
    use HasFactory;

    protected $table = 'consulta';
    protected $primaryKey = 'consulta_id';
    public $timestamps = false;

    protected $fillable = [
        'medico_id',
        'ficha_id',
        'motivo_consulta',
        'fecha',
        'tipo'
    ];

    /**
     * Relación con Ficha
     */
    public function ficha()
    {
        return $this->belongsTo(Ficha::class, 'ficha_id', 'ficha_id');
    }

    /**
     * Relación con Medico
     */
    public function medico()
    {
        return $this->belongsTo(Medico::class, 'medico_id', 'medico_id');
    }

    /**
     * Acceso al paciente a través de ficha
     */
    public function paciente()
    {
        return $this->hasOneThrough(
            Paciente::class,
            Ficha::class,
            'ficha_id', // Foreign key on Ficha table
            'paciente_id', // Foreign key on Paciente table
            'ficha_id', // Local key on Consulta table
            'paciente_id' // Local key on Ficha table
        );
    }
}