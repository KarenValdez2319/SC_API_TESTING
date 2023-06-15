<?php

namespace App\Models\MesaControl;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Custodia extends Model
{
    use HasFactory;

    protected $table = 'ope_custodia_unidad';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'tipo_custodia',
    ];
}
