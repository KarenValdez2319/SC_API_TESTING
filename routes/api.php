<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EvidenciaOperadorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'auth'], function ($router) {

    //Rutas para el login del usuario
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');
    Route::get('logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::post('registro', [AuthController::class, 'registro'])->name('auth.registro');
    Route::get('informacion_usuario', [AuthController::class, 'informacion_usuario'])->name('auth.informacio_usuario');
});

Route::group(['prefix' => 'operador'], function ($router) {

    Route::post('nuevo_registro', [EvidenciaOperadorController::class, 'nuevo_registro'])
        ->name('operadores.nuevo_registro');

    Route::get('buscar', [EvidenciaOperadorController::class, 'buscar_todos'])
        ->name('operadores.buscar_todos');

    Route::get('buscar_usuario/', [EvidenciaOperadorController::class, 'buscar_usuario'])
        ->name('operadores.buscar_usuario');

    Route::get('buscar_registro/{idEvidencia}', [EvidenciaOperadorController::class, 'buscar_registro'])
        ->name('operadores.buscar_registro');

    Route::post('buscar_fecha', [EvidenciaOperadorController::class, 'buscar_fecha'])
        ->name('operadores.buscar_fecha');

    Route::delete('eliminar_registro/{idEvidencia}', [EvidenciaOperadorController::class, 'eliminar_registro'])
        ->name('operadores.eliminar_registro');

    Route::get('unidades', [EvidenciaOperadorController::class, 'unidades']);
});
