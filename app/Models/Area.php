<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $table = 'area';            // tabla en singular
    protected $primaryKey = 'area_id';    // PK personalizada
    public $timestamps = false;           // tu tabla no tiene timestamps

    protected $fillable = [
        'nombre_area',
        'descripcion',
    ];
}