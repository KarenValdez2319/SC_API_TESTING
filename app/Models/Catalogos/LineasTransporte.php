<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineasTransporte extends Model
{
    use HasFactory;

    protected $table = 'ope_lineas_transporte';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'nombre',
    ];
}
