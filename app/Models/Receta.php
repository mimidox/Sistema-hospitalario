<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receta extends Model
{
    use HasFactory;

    protected $table = 'receta';
    protected $primaryKey = 'receta_id';
    public $timestamps = false;

    protected $fillable = [
        'tratamiento_id',
        'medicamento_id',
        'dosis',
        'duracion',
        'frecuencia'
    ];

    /**
     * Accessor para obtener la fecha de la receta
     */
    public function getFechaRecetaAttribute()
    {
        if ($this->tratamiento && $this->tratamiento->fecha_ini) {
            return \Carbon\Carbon::parse($this->tratamiento->fecha_ini)->format('d/m/Y');
        }
        return 'N/A';
    }

    /**
     * Relación con Tratamiento
     */
    public function tratamiento()
    {
        return $this->belongsTo(Tratamiento::class, 'tratamiento_id', 'tratamiento_id');
    }

    /**
     * Relación con Medicamento
     */
    public function medicamento()
    {
        return $this->belongsTo(Medicamento::class, 'medicamento_id', 'medicamento_id');
    }
}