<?php

namespace App\Models\Vistas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Viajes_Ultimo_Estatus extends Model
{
    use HasFactory;

    protected $table = 'v_ope_viajes_ultimo_estatus';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'trip',
        'tipo_unidad',
        'litros',
        'km',
        'linea_transporte',
        'fecha_cedis',
        'fecha_descarga',
        'origen',
        'id_usuario',
        'operador',
        'numero_economico',
        'placas',
        'id_estatus',
        'estatus',
        'fecha_registro',
    ];
}
