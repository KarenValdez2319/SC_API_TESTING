<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estatus extends Model
{
    use HasFactory;

    protected $table = 'ope_estatus';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'descripcion',
        'orden',
    ];
}
