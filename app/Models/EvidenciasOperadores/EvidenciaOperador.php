<?php

namespace App\Models\EvidenciasOperadores;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvidenciaOperador extends Model
{
    use HasFactory;

    protected $table = 'ope_evidencia_operadores';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'id_viaje',
        'odometro',
        'archivo_odometro',
        'fecha_ingreso',
        'litros',
        'archivo_ticket'
    ];
}
