<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicamento extends Model
{
    use HasFactory;

    protected $table = 'medicamentos';
    protected $primaryKey = 'medicamento_id';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'composicion',
        'tipo_administracion'
    ];

    /**
     * Relación con Recetas
     */
    public function recetas()
    {
        return $this->hasMany(Receta::class, 'medicamento_id', 'medicamento_id');
        
    }
}