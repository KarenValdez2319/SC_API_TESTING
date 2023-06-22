<?php

use App\Http\Controllers\Api\AdministradorController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EvidenciaOperadorController;
use App\Http\Controllers\Api\MesaControlController;
use App\Http\Controllers\Api\MonitoristaController;
use App\Http\Controllers\WebHook\TestingST;
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

Route::post('auth/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('auth/registro', [AuthController::class, 'registro'])->name('auth.registro');

Route::middleware('auth:sanctum')->prefix('auth')->group(function ($router) {

    //Rutas para el login del usuario
    Route::get('logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::get('informacion_usuario', [AuthController::class, 'informacion_usuario'])->name('auth.informacio_usuario');
});

Route::group([/* 'middleware' => ['auth:sanctum'],  */'prefix' => 'operador'], function ($router) {
    Route::get('asignacion_viaje/{idUsuario}', [EvidenciaOperadorController::class, 'obtener_viaje']);
    Route::post('nuevo_registro/{idViaje}', [EvidenciaOperadorController::class, 'nuevo_registro']);
    Route::get('buscar_usuario/', [EvidenciaOperadorController::class, 'buscar_usuario']);
    Route::get('buscar_viaje/{idViaje}', [EvidenciaOperadorController::class, 'buscar_viaje']);
    Route::post('buscar_fecha', [EvidenciaOperadorController::class, 'buscar_fecha']);
});

Route::group([/* 'middleware' => ['auth:sanctum'], */ 'prefix' => 'monitorista'], function ($router) {

    Route::get('obtener_catalogos', [MonitoristaController::class, 'obtener_catalogos']);
    Route::get('obtener_viajes', [MonitoristaController::class, 'obtener_viajes']);
    Route::post('registrar_viaje', [MonitoristaController::class, 'registrar_viaje']);
    Route::get('seguimiento_viaje/{idViaje}', [MonitoristaController::class, 'seguimiento_viaje']);
    Route::post('registro_seguimiento/{idViaje}', [MonitoristaController::class, 'registro_seguimiento']);
    Route::post('finalizar_viaje/{idViaje}', [MonitoristaController::class, 'finalizar_viaje']);
    Route::get('obtener_coordenadas/{idViaje}', [MonitoristaController::class,'obtener_coordenadas']);

});


Route::group([/* 'middleware' => ['auth:sanctum'], */ 'prefix' => 'administrador'], function ($router) {
    Route::get('buscar_viajes', [AdministradorController::class, 'buscar_viajes']);
    Route::get('buscar_viaje/{idViaje}', [AdministradorController::class, 'buscar_viaje']);
});

Route::group([/* 'middleware' => ['auth:sacntum'], */ 'prefix' => 'mesa_control'], function ($router){

    Route::get('obtener_catalogos', [MesaControlController::class, 'obtener_catalogos']);
    Route::post('solicitud_registro', [MesaControlController::class, 'solicitud_registro']);
    Route::get('obtener_solicitudes/{tripSolicitud?}', [MesaControlController::class, 'obtener_solicitudes']);
    Route::post('editar_solicitud/{tripSolicitud}', [MesaControlController::class, 'editar_solicitud']);

});

//Ruta para WebHook

Route::post('webhook/testing_st', [TestingST::class, 'st_datos']);


