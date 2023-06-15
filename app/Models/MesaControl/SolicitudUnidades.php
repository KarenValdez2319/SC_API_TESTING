<?php

namespace App\Models\MesaControl;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudUnidades extends Model
{
    use HasFactory;

    protected $table = 'ope_solicitud_unidad';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'trip',
        'email_solicitud',
        'id_nombre_cliente',
        'id_origen',
        'cantidad_retirar',
        'unidad_medida',
        'peso_carga',
        'id_tipo_unidad',
        'cantidad_unidades',
        'fecha_cedis',
        'hora_cedis',
        'cliente_entrega',
        'direccion_entrega',
        'fecha_descarga',
        'hora_descarga',
        'folio_st',
        'factura',
        'maniobras',
        'cantidad_maniobras',
        'id_custodia',
        'observacion',
    ];
}
