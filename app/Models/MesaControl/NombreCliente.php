<?php

namespace App\Models\MesaControl;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NombreCliente extends Model
{
    use HasFactory;

    protected $table = 'ope_nombre_cliente';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'nombre_cliente',
    ];
}
