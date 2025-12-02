<?php
// app/Models/Usuario.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuario';
    protected $primaryKey = 'usuario_id';
    public $timestamps = false;

    protected $fillable = [
        'username', 'nombre', 'paterno', 'materno', 'genero',
        'correo', 'contraseña', 'telefono', 'calle', 'zona',
        'municipio', 'fec_nac'
    ];

    // Relación con médico
    public function doctor()
    {
        return $this->hasOne(Doctor::class, 'usuario_id');
    }
}