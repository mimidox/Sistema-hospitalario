<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    protected $table = 'paciente';
    protected $primaryKey = 'paciente_id';
    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'tipo_de_sangre',
    ];
}