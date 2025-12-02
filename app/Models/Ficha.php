<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ficha extends Model
{
    use HasFactory;

    protected $table = 'ficha';
    protected $primaryKey = 'ficha_id';
    public $timestamps = false;

    protected $fillable = [
        'paciente_id',
        'seguro_id',
        'fecha',
        'hora',
        'estado',
        'nro_ficha'
    ];

    /**
     * Relación con Paciente
     */
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id', 'paciente_id');
    }

    /**
     * Relación con SeguroMedico
     */
    public function seguro()
    {
        return $this->belongsTo(SeguroMedico::class, 'seguro_id', 'seguro_id');
    }

    /**
     * Relación con Consulta
     */
    public function consulta()
    {
        return $this->hasOne(Consulta::class, 'ficha_id', 'ficha_id');
    }

    /**
     * Relación con Consultas (puede tener varias)
     */
    public function consultas()
    {
        return $this->hasMany(Consulta::class, 'ficha_id', 'ficha_id');
    }
}