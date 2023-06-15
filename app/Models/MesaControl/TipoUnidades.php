<?php

namespace App\Models\MesaControl;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoUnidades extends Model
{
    use HasFactory;

    protected $table = 'ope_tipo_unidad';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'tipo_unidad',
        'estatus',
    ];
}
