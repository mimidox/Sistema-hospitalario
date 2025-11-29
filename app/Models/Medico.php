<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medico extends Model
{
    protected $table = 'medico';
    protected $primaryKey = 'medico_id';
    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'especialidad',
        'nro_licencia',
        'años_experiencia',
    ];
}