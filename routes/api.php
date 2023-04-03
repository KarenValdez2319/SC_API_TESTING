<?php

use App\Http\Controllers\Api\AdministradorController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EvidenciaOperadorController;
use App\Http\Controllers\Api\MonitoristaController;
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
    Route::get('asignacion_viaje/{idUsuario}', [EvidenciaOperadorController::class, 'obtener_viaje']);
    Route::post('nuevo_registro/{idViaje}', [EvidenciaOperadorController::class, 'nuevo_registro']);
    Route::get('buscar_usuario/', [EvidenciaOperadorController::class, 'buscar_usuario']);
    Route::get('buscar_viaje/{idViaje}', [EvidenciaOperadorController::class, 'buscar_viaje']);
    Route::post('buscar_fecha', [EvidenciaOperadorController::class, 'buscar_fecha']);
});

Route::group(['prefix' => 'monitorista'], function ($router) {

    Route::get('obtener_catalogos', [MonitoristaController::class, 'obtener_catalogos']);
    Route::get('obtener_viajes', [MonitoristaController::class, 'obtener_viajes']);
    Route::post('registrar_viaje', [MonitoristaController::class, 'registrar_viaje']);
    Route::get('seguimiento_viaje/{idViaje}', [MonitoristaController::class, 'seguimiento_viaje']);
    Route::post('registro_seguimiento/{idViaje}', [MonitoristaController::class, 'registro_seguimiento']);
    Route::post('finalizar_viaje/{idViaje}', [MonitoristaController::class, 'finalizar_viaje']);
});


Route::group(['prefix' => 'administrador'], function ($router) {
    Route::get('buscar_viajes', [AdministradorController::class, 'buscar_viajes']);
    Route::get('buscar_viaje/{idViaje}', [AdministradorController::class, 'buscar_viaje']);
});