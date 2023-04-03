<?php

namespace App\Models\Monitorista;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeguimientoViaje extends Model
{
    use HasFactory;

    protected $table = 'ope_seguimiento_viajes';
    public $timestamps = false;

    protected $fillable = [
        'id_viaje',
        'id_estatus',
        'latitud',
        'longitud',
        'comentarios',
    ];
}
