<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Habitacion extends Model
{
    use HasFactory;

    protected $table = 'habitacion';
    protected $primaryKey = 'habitacion_id';
    public $timestamps = false;

    protected $fillable = [
        'nro_habitacion',
        'tipo',
        'estado'
    ];

    /**
     * Relación con Hospitalizaciones
     */
    public function hospitalizaciones()
    {
        return $this->hasMany(Hospitalizacion::class, 'habitacion_id', 'habitacion_id');
    }
}