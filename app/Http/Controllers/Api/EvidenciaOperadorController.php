<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Catalogos\Estatus;
use App\Models\EvidenciasOperadores\EvidenciaOperador;
use App\Models\EvidenciasOperadores\Unidades;
use App\Models\Monitorista\BitacoraViajes;
use App\Models\Vistas\Viajes_Ultimo_Estatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EvidenciaOperadorController extends Controller
{
    public function __construct(){
        /*  $this->middleware('auth:api', ['except' => []]); */
    }

    public function obtener_viaje($idUsuario){

        $finalizado = Estatus::where('descripcion', 'FINALIZADO')->first();

        if(empty($finalizado)){
            return response(["statusCode" => 404, "message" => "Error al obtener catalogos de estatus, contacta a TI"], 404);
        }

        //Obtener El viaje Asignado
        $viaje = Viajes_Ultimo_Estatus::whereNotIn('id_estatus', [3])->where('id_usuario', $idUsuario)->first();

        return response(["statusCode" => 200, "viaje" => $viaje], 200);
    }

    public function nuevo_registro(Request $request, $idViaje){

        $request->validate([
            'id_usuario' => ['required', 'string'],
            'odometro' => ['required', 'numeric'],
            'archivo_odometro' => ['required', 'image', 'mimes:jpg,png,jpeg,gif,svg'],
            'litros' => ['required', 'numeric'],
            'archivo_ticket' => ['required', 'image', 'mimes:jpg,png,jpeg,gif,svg'],
        ]);

        $id_usuario = $request->input('id_usuario');
        $odometro = $request->input('odometro');
        $litros = $request->input('litros');

        //Validar que el viaje no este cerrado
        $viaje = BitacoraViajes::where('id', $idViaje)->first();

        if(empty($viaje)){
            return response(["statusCode" => 404, "message" => "El viaje no se encontro en la base de datos"], 404);
        }

        //VAlidar Estatus
        $viajeEstatus = Viajes_Ultimo_Estatus::where('id', $idViaje)->first();

        if($viajeEstatus->id_estatus == 3){
            return response(["statusCode" => 404, "message" => "El viaje a sido cerrado, porfavor actualiza la pagina"], 404);
        }

        $archivo_ticket = $request->file('archivo_ticket');
        $archivo_odometro = $request->file('archivo_odometro');

        $nombre_archivo_odo = date('dmY_His') . '_' . $archivo_odometro->getClientOriginalName();
        $archivo_odometro->move(public_path('evidencia'), $nombre_archivo_odo);

        $nombre_archivo_tic = date('dmY_His') . '_' . $archivo_ticket->getClientOriginalName();
        $archivo_ticket->move(public_path('evidencia'), $nombre_archivo_tic);

        $registro = EvidenciaOperador::create([
            'id_usuario' => $id_usuario,
            'id_viaje' => $idViaje,
            'odometro' => $odometro,
            'archivo_odometro' => $nombre_archivo_odo,
            'litros' => $litros,
            'archivo_ticket' => $nombre_archivo_tic,
        ]);

        return response()->json([
            'registro' => $registro,
        ]);
    }

    public function buscar_fecha(Request $request){

         $validated = $request->validate([
            'fecha_inicio' => 'required',
            'fecha_fin' => 'required'
        ]);

        $fecha_inicio = $request->input('fecha_inicio') .' '. '00:00:01';
        $fecha_fin = $request->input('fecha_fin') .' '. '23:59:59';

        if ($fecha_fin < $fecha_inicio) {
            return response([
                'status' => '500',
                'message' => 'La fecha final no puede ser mayor a la inicial'
            ], 500);
        }

        $buscar_fecha = Viajes_Ultimo_Estatus::select('id', 'trip', 'tipo_unidad', 'litros', 'km', 'linea_transporte', 'origen', 'operador', 'numero_economico', 'placas', 'estatus', 'fecha_registro')
            ->whereBetween(DB::raw('date_format(fecha_registro, "%Y-%m-%d %H:%i:%s")'), [$fecha_inicio, $fecha_fin])
            ->get();


        if (!$buscar_fecha->isEmpty()) {

            $registros = [];

            for ($i = 0; $i < count($buscar_fecha); $i++) {

                $datos = [
                    'id' => $buscar_fecha[$i]->id,
                    'trip' => $buscar_fecha[$i]->trip,
                    'tipo_unidad' => $buscar_fecha[$i]->tipo_unidad,
                    'origen' => $buscar_fecha[$i]->origen,
                    'operador' => $buscar_fecha[$i]->operador,
                    'estatus' => $buscar_fecha[$i]->estatus,
                    'no_economico' => $buscar_fecha[$i]->no_economico,
                    'placas' => $buscar_fecha[$i]->placas,
                    'km' => $buscar_fecha[$i]->km,
                    'archivo_odometro' => asset('/evidencia/') . "/" . $buscar_fecha[$i]->archivo_odometro,
                    'litros' => $buscar_fecha[$i]->litros,
                    'archivo_ticket' => asset('/evidencia/') . "/" . $buscar_fecha[$i]->archivo_ticket,
                    'fecha_registro' => $buscar_fecha[$i]->fecha_registro,
                ];

                array_push($registros, $datos);
            }

            return response()->json([
                'status' => 'sucess',
                'registros' => $registros,
            ], 200);
        }


        return response()->json([
            'status' => 'error',
            'message' => 'No se encontraron datos en la fecha seleccionada'
        ], 404);
    }
}
