<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tratamiento extends Model
{
    use HasFactory;

    protected $table = 'tratamiento';
    protected $primaryKey = 'tratamiento_id';
    public $timestamps = false;

    protected $fillable = [
        'historial_id',
        'tipo_tratamiento',
        'fecha_ini',
        'fecha_fin'
    ];

    /**
     * Relación con HistorialMedico
     */
    public function historial()
    {
        return $this->belongsTo(HistorialMedico::class, 'historial_id', 'historial_id');
    }

    /**
     * Relación con Recetas
     */
    public function recetas()
    {
        return $this->hasMany(Receta::class, 'tratamiento_id', 'tratamiento_id');
    }
}