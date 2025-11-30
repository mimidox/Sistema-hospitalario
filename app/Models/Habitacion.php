<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Habitacion extends Model
{
    protected $table = 'habitacion';
    protected $primaryKey = 'habitacion_id';
    public $timestamps = false;

    protected $fillable = [
        'nro_habitacion',
        'tipo',
        'estado',
    ];
}