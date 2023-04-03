<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unidades extends Model
{
    use HasFactory;

    protected $table = 'ope_unidades';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'numero_economico',
        'placas',
        'estatus',
    ];
}
