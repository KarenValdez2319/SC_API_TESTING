<?php

namespace App\Models\MesaControl;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Origen extends Model
{
    use HasFactory;

    protected $table = 'ope_origen';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'origen',
        'latitud',
        'longitud',
    ];
}
