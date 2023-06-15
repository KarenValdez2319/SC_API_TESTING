<?php

namespace App\Models\MesaControl;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UbicacionCarga extends Model
{
    use HasFactory;

    protected $table = 'ope_ubicacion_carga';
    public $timestamp = false;

    protected $fillable = [
        'id',
        'nombre_cliente',
        'origen_carga',
        'latitud',
        'longitud'
    ];
}
