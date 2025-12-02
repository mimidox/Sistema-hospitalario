<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medico extends Model
{
    use HasFactory;

    protected $table = 'medico';
    protected $primaryKey = 'medico_id';
    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'especialidad',
        'nro_licencia',
        'años_experiencia'
    ];

    /**
     * Relación con Usuario
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id', 'usuario_id');
    }

    /**
     * Relación con Consultas
     */
    public function consultas()
    {
        return $this->hasMany(Consulta::class, 'medico_id', 'medico_id');
    }

    /**
     * Relación con Hospitalizaciones
     */
    public function hospitalizaciones()
    {
        return $this->hasMany(Hospitalizacion::class, 'medico_id', 'medico_id');
    }
}