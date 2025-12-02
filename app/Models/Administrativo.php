<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Administrativo extends Model
{
    use HasFactory;

    protected $table = 'administrativo';
    protected $primaryKey = 'administrativo_id';
    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'area_id',
        'cargo'
    ];

    /**
     * Relación con Usuario
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id', 'usuario_id');
    }

    /**
     * Relación con Area
     */
    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id', 'area_id');
    }
}