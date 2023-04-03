<?php

namespace App\Models\Monitorista;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BitacoraViajes extends Model
{
    use HasFactory;

    protected $table = 'ope_bitacora_viajes';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'viaje',
        'tipo_unidad',
        'id_unidad',
        'id_linea_transporte',
        'origen',
        'fecha_entrega',
        'fecha_inicio',
        'litros',
        'km',
        'id_usuario',
    ];
}
