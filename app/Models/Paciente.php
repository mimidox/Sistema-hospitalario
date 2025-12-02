<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    use HasFactory;

    protected $table = 'paciente';
    protected $primaryKey = 'paciente_id';
    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'tipo_de_sangre'
    ];

    /**
     * Relación con Usuario
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id', 'usuario_id');
    }

    /**
     * Relación con HistorialMedico
     */
    public function historial()
    {
        return $this->hasOne(HistorialMedico::class, 'paciente_id', 'paciente_id');
    }

    /**
     * Relación con Fichas
     */
    public function fichas()
    {
        return $this->hasMany(Ficha::class, 'paciente_id', 'paciente_id');
    }

    /**
     * Relación con Consultas a través de Fichas
     */
    public function consultas()
    {
        return $this->hasManyThrough(
            Consulta::class,
            Ficha::class,
            'paciente_id', // Foreign key on Ficha table
            'ficha_id', // Foreign key on Consulta table
            'paciente_id', // Local key on Paciente table
            'ficha_id' // Local key on Ficha table
        );
    }

    /**
     * Relación con Hospitalizaciones
     */
    public function hospitalizaciones()
    {
        return $this->hasMany(Hospitalizacion::class, 'paciente_id', 'paciente_id');
    }
}