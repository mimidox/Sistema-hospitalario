<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeguroMedico extends Model
{
    use HasFactory;

    protected $table = 'seguro_medico';
    protected $primaryKey = 'seguro_id';
    public $timestamps = false;

    protected $fillable = [
        'tipo_seguro',
        'poliza'
    ];

    /**
     * Relación con Fichas
     */
    public function fichas()
    {
        return $this->hasMany(Ficha::class, 'seguro_id', 'seguro_id');
    }
}