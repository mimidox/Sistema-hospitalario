<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected $table = 'area';
    protected $primaryKey = 'area_id';
    public $timestamps = false;

    protected $fillable = [
        'nombre_area',
        'descripcion'
    ];

    /**
     * Relación con Administrativos
     */
    public function administrativos()
    {
        return $this->hasMany(Administrativo::class, 'area_id', 'area_id');
    }
}