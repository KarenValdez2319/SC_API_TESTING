<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    protected $table = 'usuarios';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'nombre',
        'apellidos',
        'usuario',
        'password',
        'rol',
        'estatus'
    ];


}
