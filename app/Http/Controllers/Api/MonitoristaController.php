<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Catalogos\Estatus;
use App\Models\Catalogos\LineasTransporte;
use App\Models\Catalogos\Unidades;
use App\Models\MesaControl\SolicitudUnidades;
use App\Models\MesaControl\UbicacionCarga;
use App\Models\Monitorista\BitacoraViajes;
use App\Models\Monitorista\SeguimientoViaje;
use App\Models\User;
use App\Models\Vistas\Viajes_Ultimo_Estatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonitoristaController extends Controller
{
    //
    public function obtener_catalogos()
    {

        //Obtener Lineas de Transporte
        $lineas = LineasTransporte::all();

        if ($lineas->isEmpty()) {
            return response(["statusCode" => 404, "message" => "No existen lineas de transporte"], 404);
        }

        //Economicos
        $eco = Unidades::where('estatus', 1)->get();
        if ($eco->isEmpty()) {
            return response(["statusCode" => 404, "message" => "No existen numeros economicos"], 404);
        }

        //Obtener Operadores
        $operadores = User::select('id', DB::raw('concat(nombre, " ", apellidos) as nombre'))->where('rol', 'OPERADOR')->get();

        if ($operadores->isEmpty()) {
            return response(["statusCode" => 404, "message" => "No existen operadores"], 404);
        }

        $ubicacion_carga = UbicacionCarga::all();

        if($ubicacion_carga->isEmpty()){
            return response(['status' => 404, 'message' => 'No existen Ubicacion Carga' ],404);
        }

        return response(["statusCode" => 200, "lineas_transporte" => $lineas, "economicos" => $eco, "operadores" => $operadores, 'ubicacion_carga' => $ubicacion_carga], 200);
    }

    public function obtener_viajes()
    {

        $viajes = Viajes_Ultimo_Estatus::whereNotIn('id_estatus', [3])->get();

        return response(["statusCode" => 200, "viajes" => $viajes], 200);
    }

    public function registrar_viaje(Request $request)
    {

        $validated = $request->validate([
            'trip' => 'required|max:40',
            'tipo_unidad' => 'required',
            'eco' => 'required',
            'linea' => 'required',
            'origen' => 'required',
            'fecha_cedis' => 'required',
            'fecha_descarga' => 'required',
            'litros' => 'required',
            'km' => 'required',
            'operador' => 'required',
            'origen_carga' => 'required'
        ]);

        $trip = strtoupper($request->input('trip'));
        $tipo_unidad = strtoupper($request->input('tipo_unidad'));
        $id_unidad = $request->input('eco');
        $id_linea_transporte = $request->input('linea');
        $origen = strtoupper($request->input('origen'));
        $fecha_cedis = $request->input('fecha_cedis');
        $fecha_descarga = $request->input('fecha_descarga');
        $litros = $request->input('litros');
        $km = $request->input('km');
        $idUsuario = $request->input('operador');
        $origen_carga = $request->input('origen_carga');

        //Validar que la unidad no este ocupada
        $validarUnidad = Unidades::where('id', $id_unidad)->first();

        if ($validarUnidad->estatus == 0) {
            return response(["statusCode" => 500, "message" => "La unidad ya esta ocupada"], 500);
        }

        //Validar que no exista el viaje
        $existeViaje = BitacoraViajes::where('trip', $trip)->get();

        $validacionOperador = User::where('id', $idUsuario)->where('estatus', 1)->first();

        if (!$validacionOperador) {

            return response(['status' => 500, 'message' => 'El operador ya tiene un Viaje asignado'], 500);
        }

        BitacoraViajes::create([
            'trip' => $trip,
            'tipo_unidad' => $tipo_unidad,
            'id_unidad' => $id_unidad,
            'id_linea_transporte' => $id_linea_transporte,
            'origen' => $origen,
            'fecha_cedis' => $fecha_cedis,
            'fecha_descarga' => $fecha_descarga,
            'litros' => $litros,
            'km' => $km,
            'id_usuario' => $idUsuario,
            'origen_carga' => $origen_carga
        ]);

        SolicitudUnidades::where('trip', $trip)->update([
            'estatus' => 0
        ]);

        //Actualizar unidad a ocupada
        Unidades::where('id', $id_unidad)->update(["estatus" => 0]);

        User::where('id', $idUsuario)->update(['estatus' => 0]);

        $viajes = Viajes_Ultimo_Estatus::whereNotIn('id_estatus', [3])->get();

        $solicitudes = SolicitudUnidades::select('ope_solicitud_unidad.id', 'ope_solicitud_unidad.trip', 'ope_solicitud_unidad.email_solicitud', 'ope_nombre_cliente.nombre_cliente', 'ope_origen.origen', 'ope_solicitud_unidad.cantidad_retirar', 'ope_solicitud_unidad.unidad_medida', 'ope_solicitud_unidad.peso_carga', 'ope_tipo_unidad.tipo_unidad', 'ope_solicitud_unidad.cantidad_unidades', 'ope_solicitud_unidad.fecha_cedis', 'ope_solicitud_unidad.hora_cedis', 'ope_solicitud_unidad.cliente_entrega', 'ope_solicitud_unidad.direccion_entrega', 'ope_solicitud_unidad.fecha_descarga', 'ope_solicitud_unidad.hora_descarga', 'ope_solicitud_unidad.folio_st', 'ope_solicitud_unidad.factura', 'ope_solicitud_unidad.maniobras', 'ope_solicitud_unidad.cantidad_maniobras', 'ope_custodia_unidad.tipo_custodia', 'ope_solicitud_unidad.observacion', 'ope_solicitud_unidad.estatus')
            ->join('ope_nombre_cliente', 'ope_nombre_cliente.id', '=', 'ope_solicitud_unidad.id_nombre_cliente')
            ->join('ope_origen', 'ope_origen.id', '=', 'ope_solicitud_unidad.id_origen')
            ->join('ope_tipo_unidad', 'ope_tipo_unidad.id', '=', 'ope_solicitud_unidad.id_tipo_unidad')
            ->join('ope_custodia_unidad', 'ope_custodia_unidad.id', '=', 'ope_solicitud_unidad.id_custodia')
            ->where('ope_solicitud_unidad.estatus', 1)
            ->orderBy('ope_solicitud_unidad.id')
            ->get();

        return response(["statusCode" => 200, 'message' => 'Todo bien   ', 'solicitudes' => $solicitudes], 200);
    }

    public function seguimiento_viaje($idViaje)
    {

        $seguimiento = SeguimientoViaje::select('ope_seguimiento_viajes.id', 'ope_seguimiento_viajes.latitud', 'ope_seguimiento_viajes.longitud', 'ope_estatus.descripcion', 'ope_seguimiento_viajes.comentarios', 'ope_seguimiento_viajes.fecha_registro')
            ->where('id_viaje', $idViaje)
            ->join('ope_estatus', 'ope_estatus.id', '=', 'ope_seguimiento_viajes.id_estatus')
            ->get();

        //Obtener Estatus
        $estatus = Estatus::whereNotIn('descripcion', ["FINALIZADO"])->orderBy('descripcion')->get();

        if ($estatus->isEmpty()) {
            return response(["statusCode" => 404, "message" => "No existen estatus"], 404);
        }

        return response(["statusCode" => 200, "seguimientos" => $seguimiento, "estatus" => $estatus], 200);
    }

    public function registro_seguimiento(Request $request, $idViaje)
    {

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

    public function finalizar_viaje($idViaje)
    {

        //Validar que exista el viaje
        $viaje = BitacoraViajes::where('id', $idViaje)->first();

        if (empty($viaje)) {
            return response(["statusCode" => 404, "message" => "El viaje no fue encontrado"], 404);
        }

        $finalizado = Estatus::where('descripcion', 'FINALIZADO')->first();

        if (empty($finalizado)) {
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

        BitacoraViajes::where('id', $idViaje)->update(['id_estatus' => 3]);

        User::where('id', $viaje->id_usuario)->update(['estatus' => 1]);

        $viajes = Viajes_Ultimo_Estatus::whereNotIn('id_estatus', [3])->get();

        return response(["statusCode" => 200, "viajes" => $viajes], 200);
    }

    public function obtener_coordenadas($idViaje)
    {

        $buscar_coordenadas = SeguimientoViaje::where('id_viaje', $idViaje)->get();

        $obtener_promedio = DB::select('call p_mapa_promedio(?)',array($idViaje));

        $centroMapa = [$obtener_promedio[0]->promedio_latitud, $obtener_promedio[0]->promedio_longitud];

        $posiciones = [];

        for ($i = 0; $i < count($buscar_coordenadas); $i++) {

            $datos = [
                'latitud' => $buscar_coordenadas[$i]->latitud,
                'longitud' => $buscar_coordenadas[$i]->longitud
              ];

            array_push($posiciones, $datos);

        }

        return response([
            'status' => 200,
            'coordenadas' => $posiciones,
            'centro_mapa' => $centroMapa
        ], 200);
    }
}
