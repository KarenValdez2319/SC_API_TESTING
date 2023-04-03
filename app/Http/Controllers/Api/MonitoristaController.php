<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Catalogos\Estatus;
use App\Models\Catalogos\LineasTransporte;
use App\Models\Catalogos\Unidades;
use App\Models\Monitorista\BitacoraViajes;
use App\Models\Monitorista\SeguimientoViaje;
use App\Models\User;
use App\Models\Vistas\Viajes_Ultimo_Estatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonitoristaController extends Controller
{
    //
    public function obtener_catalogos(){

        //Obtener Lineas de Transporte
        $lineas = LineasTransporte::all();

        if($lineas->isEmpty()){
            return response(["statusCode" => 404, "message" => "No existen lineas de transporte"], 404);
        }

        //Economicos
        $eco = Unidades::where('estatus', 1)->get();
        if($eco->isEmpty()){
            return response(["statusCode" => 404, "message" => "No existen numeros economicos"], 404);
        }

        //Obtener Operadores
        $operadores = User::select('id', DB::raw('concat(nombre, " ", apellidos) as nombre'))->where('rol', 'OPERADOR')->get();

        if($operadores->isEmpty()){
            return response(["statusCode" => 404, "message" => "No existen operadores"], 404);
        }


        return response(["statusCode" => 200, "lineas_transporte" => $lineas, "economicos" => $eco, "operadores" => $operadores], 200);
    }

    public function obtener_viajes(){

        $viajes = Viajes_Ultimo_Estatus::whereNotIn('id_estatus', [3])->get();
        
        return response(["statusCode" => 200, "viajes" => $viajes], 200);
    }

    public function registrar_viaje(Request $request){

        $validated = $request->validate([
            'viaje' => 'required|max:40',
            'tipo_unidad' => 'required',
            'eco' => 'required',
            'linea' => 'required',
            'origen' => 'required',
            'fecha_entrega' => 'required',
            'fecha_inicio' => 'required',
            'litros' => 'required',
            'km' => 'required',
            'operador' => 'required',
        ]);

        $viaje = strtoupper($request->input('viaje'));
        $tipoUnidad = strtoupper($request->input('tipo_unidad'));
        $idUnidad = $request->input('eco');
        $idLineaTransporte = $request->input('linea');
        $origen = strtoupper($request->input('origen'));
        $fechaEntrega = $request->input('fecha_entrega');
        $fechaInicio = $request->input('fecha_inicio');
        $litros = $request->input('litros');
        $km = $request->input('km');
        $idUsuario = $request->input('operador');

        //Validar que la unidad no este ocupada
        $validarUnidad = Unidades::where('id', $idUnidad)->first();

        if($validarUnidad->estatus == 0){
            return response(["statusCode" => 500, "message" => "La unidad ya esta ocupada"], 500);
        }

        //Validar que no exista el viaje
        $existeViaje = BitacoraViajes::where('viaje', $viaje)->get();

        if(!$existeViaje->isEmpty()){
            return response(["statusCode" => 500, "message" => "Ya existe un viaje registrado previamente"], 500);
        }

        //Validar que no se pueda asignar un viaje hasta que este en estaus finalizado
        $validacionOperador = BitacoraViajes::where('id_usuario', $idUsuario)->get();

        if(!$validacionOperador->isEmpty()){
            return response(["statusCode" => 500, "message" => "El operador ya tiene un viaje asignado"], 500);
        }

        BitacoraViajes::create([
            'viaje' => $viaje,
            'tipo_unidad' => $tipoUnidad,
            'id_unidad' => $idUnidad,
            'id_linea_transporte' => $idLineaTransporte,
            'origen' => $origen,
            'fecha_entrega' => $fechaEntrega,
            'fecha_inicio' => $fechaInicio,
            'litros' => $litros,
            'km' => $km,
            'id_usuario' => $idUsuario,
        ]);

        //Actualizar unidad a ocupada
        Unidades::where('id', $idUnidad)->update(["estatus" => 0]);

        $viajes = Viajes_Ultimo_Estatus::whereNotIn('id_estatus', [3])->get();

        return response(["statusCode" => 200, "viajes" => $viajes], 200);
        
    }

    public function seguimiento_viaje($idViaje){

        $seguimiento = SeguimientoViaje::select('ope_seguimiento_viajes.id', 'ope_seguimiento_viajes.latitud', 'ope_seguimiento_viajes.longitud', 'ope_estatus.descripcion', 'ope_seguimiento_viajes.comentarios', 'ope_seguimiento_viajes.fecha_registro')
                                        ->where('id_viaje', $idViaje)
                                        ->join('ope_estatus', 'ope_estatus.id', '=', 'ope_seguimiento_viajes.id_estatus')
                                        ->get();

        //Obtener Estatus
        $estatus = Estatus::whereNotIn('descripcion', ["FINALIZADO"])->orderBy('orden')->get();

        if($estatus->isEmpty()){
            return response(["statusCode" => 404, "message" => "No existen estatus"], 404);
        }

        return response(["statusCode" => 200, "seguimientos" => $seguimiento, "estatus" => $estatus], 200);
    }

    public function registro_seguimiento(Request $request, $idViaje){

        $validated = $request->validate([
            'latitud' => 'required',
            'longitud' => 'required',
            'comentarios' => 'required',
            'estatus' => 'required',
        ]);

        $latitud = $request->input('latitud');
        $longitud = $request->input('longitud');
        $comentario = strtoupper($request->input('comentarios'));
        $idEstaus = $request->input('estatus');

        SeguimientoViaje::create([
            "id_viaje" => $idViaje,
            "id_estatus" => $idEstaus,
            "latitud" => $latitud,
            "longitud" => $longitud,
            "comentarios" => $comentario,
        ]);

        $seguimiento = SeguimientoViaje::select('ope_seguimiento_viajes.id', 'ope_seguimiento_viajes.latitud', 'ope_seguimiento_viajes.longitud', 'ope_estatus.descripcion', 'ope_seguimiento_viajes.comentarios', 'ope_seguimiento_viajes.fecha_registro')
                                        ->where('id_viaje', $idViaje)
                                        ->join('ope_estatus', 'ope_estatus.id', '=', 'ope_seguimiento_viajes.id_estatus')
                                        ->get();

        return response(["statusCode" => 200, "seguimientos" => $seguimiento], 200);
    }

    public function finalizar_viaje($idViaje){

        //Validar que exista el viaje
        $viaje = BitacoraViajes::where('id', $idViaje)->first();

        if(empty($viaje)){
            return response(["statusCode" => 404, "message" => "El viaje no fue encontrado"], 404);
        }

        $finalizado = Estatus::where('descripcion', 'FINALIZADO')->first();

        if(empty($finalizado)){
            return response(["statusCode" => 404, "message" => "Error al obtener catalogos de estatus, contacta a TI"], 404);
        }

        //Actualizar a Finalizado
        SeguimientoViaje::create([
            'id_viaje' => $idViaje,
            'id_estatus' => 3,
            'latitud' => 0,
            'longitud' => 0,
            'comentarios' => strtoupper('Viaje Finalizado'),
        ]);

        //Actualizar Unidad
        Unidades::where('id', $viaje->id_unidad)->update(["estatus" => 1]);

        $viajes = Viajes_Ultimo_Estatus::whereNotIn('id_estatus', [3])->get();

        return response(["statusCode" => 200, "viajes" => $viajes], 200);
    }
}
