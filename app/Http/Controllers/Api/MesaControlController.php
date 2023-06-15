<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MesaControl\Custodia;
use App\Models\MesaControl\NombreCliente;
use App\Models\MesaControl\Origen;
use App\Models\MesaControl\SolicitudUnidades;
use App\Models\MesaControl\TipoUnidades;
use App\Models\MesaControl\UbicacionCarga;
use Illuminate\Http\Request;

class MesaControlController extends Controller
{
    public function obtener_catalogos()
    {

        $nombre_cliente = NombreCliente::all();

        if ($nombre_cliente->isEmpty()) {
            return response(['statusCode' => 404, 'message' => 'No existen Clientes'], 404);
        }

        $origen = Origen::all();

        if ($origen->isEmpty()) {
            return response(['statusCode' => 404, 'message' => 'No existen Origenes'], 404);
        }

        $tipo_unidad = TipoUnidades::all();

        if ($tipo_unidad->isEmpty()) {
            return response(['statusCode' => 404, 'message' => 'No existen Unidades'], 404);
        }

        $custodia = Custodia::all();

        if ($custodia->isEmpty()) {
            return response(['statusCode' => 404, 'message' => 'No existen Custodia']);
        }

        return response(['statusCode' => 200, 'clientes' => $nombre_cliente, 'origen' => $origen, 'tipo_unidades' => $tipo_unidad, 'custodia' => $custodia], 200);

    }

    public function solicitud_registro(Request $request)
    {

        $header = $request->header('Accept', 'application/json');

        if ($header == 'application/json') {

            // return response(['message' => 'Todo bien']);

            $validated = $request->validate([

                'email_solicitud' => 'required',
                'id_nombre_cliente' => 'required',
                'id_origen' => 'required',
                'cantidad_retirar' => 'required',
                'unidad_medida' => 'required',
                'peso_carga' => 'required',
                'id_tipo_unidad' => 'required',
                'cantidad_unidades' => 'required',
                'fecha_cedis' => 'required',
                'hora_cedis' => 'required',
                'cliente_entrega' => 'required',
                'direccion_entrega' => 'required',
                'fecha_descarga' => 'required',
                'hora_descarga' => 'required',
                'folio_st' => 'required',
                'factura' => 'required',
                'maniobras' => 'required',
                'cantidad_maniobras' => 'required',
                'id_custodia' => 'required',
                'observacion' => 'required'
            ]);

            $email_solicitud = $request->input('email_solicitud');
            $nombre_cliente = $request->input('id_nombre_cliente');
            $origen = $request->input('id_origen');
            $cantidad_retirar = strtoupper($request->input('cantidad_retirar'));
            $unidad_medida = strtoupper($request->input('unidad_medida'));
            $peso_carga = strtoupper($request->input('peso_carga'));
            $tipo_unidad = $request->input('id_tipo_unidad');
            $cantidad_unidades = strtoupper($request->input('cantidad_unidades'));
            $fecha_cedis = $request->input('fecha_cedis');
            $hora_cedis = $request->input('hora_cedis');
            $cliente_entrega = strtoupper($request->input('cliente_entrega'));
            $direccion_entrega = strtoupper($request->input('direccion_entrega'));
            $fecha_descarga = $request->input('fecha_descarga');
            $hora_descarga = $request->input('hora_descarga');
            $folio_st = strtoupper($request->input('folio_st'));
            $factura = strtoupper($request->input('factura'));
            $maniobras = strtoupper($request->input('maniobras'));
            $cantidad_maniobras = strtoupper($request->input('cantidad_maniobras'));
            $custodia = $request->input('id_custodia');
            $observacion = strtoupper($request->input('observacion'));

            $registro = SolicitudUnidades::create([

                'email_solicitud' => $email_solicitud,
                'id_nombre_cliente' => $nombre_cliente,
                'id_origen' => $origen,
                'cantidad_retirar' => $cantidad_retirar,
                'unidad_medida' => $unidad_medida,
                'peso_carga' => $peso_carga,
                'id_tipo_unidad' => $tipo_unidad,
                'cantidad_unidades' => $cantidad_unidades,
                'fecha_cedis' => $fecha_cedis,
                'hora_cedis' => $hora_cedis,
                'cliente_entrega' => $cliente_entrega,
                'direccion_entrega' => $direccion_entrega,
                'fecha_descarga' => $fecha_descarga,
                'hora_descarga' => $hora_descarga,
                'folio_st' => $folio_st,
                'factura' => $factura,
                'maniobras' => $maniobras,
                'cantidad_maniobras' => $cantidad_maniobras,
                'id_custodia' => $custodia,
                'observacion' => $observacion,
                'estatus' => 1

            ]);

            $trip_consecutivo =  date('y') . '-' . '00' . $registro->id;

            SolicitudUnidades::where('id', $registro->id)->update(['trip' => $trip_consecutivo]);

            $solicitudes = SolicitudUnidades::select('ope_solicitud_unidad.id', 'ope_solicitud_unidad.trip', 'ope_solicitud_unidad.email_solicitud', 'ope_nombre_cliente.nombre_cliente', 'ope_origen.origen', 'ope_solicitud_unidad.cantidad_retirar', 'ope_solicitud_unidad.unidad_medida', 'ope_solicitud_unidad.peso_carga', 'ope_tipo_unidad.tipo_unidad', 'ope_solicitud_unidad.cantidad_unidades', 'ope_solicitud_unidad.fecha_cedis', 'ope_solicitud_unidad.hora_cedis', 'ope_solicitud_unidad.cliente_entrega', 'ope_solicitud_unidad.direccion_entrega', 'ope_solicitud_unidad.fecha_descarga', 'ope_solicitud_unidad.hora_descarga', 'ope_solicitud_unidad.folio_st', 'ope_solicitud_unidad.factura', 'ope_solicitud_unidad.maniobras', 'ope_solicitud_unidad.cantidad_maniobras', 'ope_custodia_unidad.tipo_custodia', 'ope_solicitud_unidad.observacion', 'ope_solicitud_unidad.estatus' )
            ->join('ope_nombre_cliente', 'ope_nombre_cliente.id', '=', 'ope_solicitud_unidad.id_nombre_cliente')
            ->join('ope_origen', 'ope_origen.id', '=', 'ope_solicitud_unidad.id_origen')
            ->join('ope_tipo_unidad', 'ope_tipo_unidad.id', '=', 'ope_solicitud_unidad.id_tipo_unidad')
            ->join('ope_custodia_unidad', 'ope_custodia_unidad.id', '=', 'ope_solicitud_unidad.id_custodia')
            ->where('ope_solicitud_unidad.estatus', 1)
            ->orderBy('ope_solicitud_unidad.id')
            ->get();

            return response([
                'status' => 200,
                'solicitudes' => $solicitudes
            ],200);

        }

        return response([
            'status' => 500,
            'message' => 'Error en peticion, Header debe contener "Key:Accept", "Value:application/json"'
        ], 500);
    }

    public function editar_solicitud(Request $request, $tripSolicitud){

        $header = $request->header('Accept', 'application/json');

        if($header == 'application/json'){

            if($tripSolicitud != null) {

                $buscar_solicitud = SolicitudUnidades::where('trip', $tripSolicitud)->first();

                if(empty($buscar_solicitud)){
                    return response([
                        'status' => 404,
                        'message' => 'No se encontraron datos'
                    ]);
                }

                $validated = $request->validate([

                    'email_solicitud' => 'required',
                    'id_nombre_cliente' => 'required',
                    'id_origen' => 'required',
                    'cantidad_retirar' => 'required',
                    'unidad_medida' => 'required',
                    'peso_carga' => 'required',
                    'id_tipo_unidad' => 'required',
                    'cantidad_unidades' => 'required',
                    'fecha_cedis' => 'required',
                    'hora_cedis' => 'required',
                    'cliente_entrega' => 'required',
                    'direccion_entrega' => 'required',
                    'fecha_descarga' => 'required',
                    'hora_descarga' => 'required',
                    'folio_st' => 'required',
                    'factura' => 'required',
                    'maniobras' => 'required',
                    'cantidad_maniobras' => 'required',
                    'id_custodia' => 'required',
                    'observacion' => 'required'

                ]);

                $email_solicitud = $request->input('email_solicitud');
                $nombre_cliente = $request->input('id_nombre_cliente');
                $origen = $request->input('id_origen');
                $cantidad_retirar = strtoupper($request->input('cantidad_retirar'));
                $unidad_medida = strtoupper($request->input('unidad_medida'));
                $peso_carga = strtoupper($request->input('peso_carga'));
                $tipo_unidad = $request->input('id_tipo_unidad');
                $cantidad_unidades = strtoupper($request->input('cantidad_unidades'));
                $fecha_cedis = $request->input('fecha_cedis');
                $hora_cedis = $request->input('hora_cedis');
                $cliente_entrega = strtoupper($request->input('cliente_entrega'));
                $direccion_entrega = strtoupper($request->input('direccion_entrega'));
                $fecha_descarga = $request->input('fecha_descarga');
                $hora_descarga = $request->input('hora_descarga');
                $folio_st = strtoupper($request->input('folio_st'));
                $factura = strtoupper($request->input('factura'));
                $maniobras = strtoupper($request->input('maniobras'));
                $cantidad_maniobras = strtoupper($request->input('cantidad_maniobras'));
                $custodia = $request->input('id_custodia');
                $observacion = strtoupper($request->input('observacion'));

                SolicitudUnidades::where('trip', $tripSolicitud)
                ->update([

                    'email_solicitud' => $email_solicitud,
                    'id_nombre_cliente' => $nombre_cliente,
                    'id_origen' => $origen,
                    'cantidad_retirar' => $cantidad_retirar,
                    'unidad_medida' => $unidad_medida,
                    'peso_carga' => $peso_carga,
                    'id_tipo_unidad' => $tipo_unidad,
                    'cantidad_unidades' => $cantidad_unidades,
                    'fecha_cedis' => $fecha_cedis,
                    'hora_cedis' => $hora_cedis,
                    'cliente_entrega' => $cliente_entrega,
                    'direccion_entrega' => $direccion_entrega,
                    'fecha_descarga' => $fecha_descarga,
                    'hora_descarga' => $hora_descarga,
                    'folio_st' => $folio_st,
                    'factura' => $factura,
                    'maniobras' => $maniobras,
                    'cantidad_maniobras' => $cantidad_maniobras,
                    'id_custodia' => $custodia,
                    'observacion' => $observacion

                ]);

                $solicitudes = SolicitudUnidades::select('ope_solicitud_unidad.id', 'ope_solicitud_unidad.trip', 'ope_solicitud_unidad.email_solicitud', 'ope_nombre_cliente.nombre_cliente', 'ope_origen.origen', 'ope_solicitud_unidad.cantidad_retirar', 'ope_solicitud_unidad.unidad_medida', 'ope_solicitud_unidad.peso_carga', 'ope_tipo_unidad.tipo_unidad', 'ope_solicitud_unidad.cantidad_unidades', 'ope_solicitud_unidad.fecha_cedis', 'ope_solicitud_unidad.hora_cedis', 'ope_solicitud_unidad.cliente_entrega', 'ope_solicitud_unidad.direccion_entrega', 'ope_solicitud_unidad.fecha_descarga', 'ope_solicitud_unidad.hora_descarga', 'ope_solicitud_unidad.folio_st', 'ope_solicitud_unidad.factura', 'ope_solicitud_unidad.maniobras', 'ope_solicitud_unidad.cantidad_maniobras', 'ope_custodia_unidad.tipo_custodia', 'ope_solicitud_unidad.observacion', 'ope_solicitud_unidad.estatus' )
                ->join('ope_nombre_cliente', 'ope_nombre_cliente.id', '=', 'ope_solicitud_unidad.id_nombre_cliente')
                ->join('ope_origen', 'ope_origen.id', '=', 'ope_solicitud_unidad.id_origen')
                ->join('ope_tipo_unidad', 'ope_tipo_unidad.id', '=', 'ope_solicitud_unidad.id_tipo_unidad')
                ->join('ope_custodia_unidad', 'ope_custodia_unidad.id', '=', 'ope_solicitud_unidad.id_custodia')
                ->orWhere('estatus', 1)
                ->orderBy('ope_solicitud_unidad.id')
                ->get();

                return response([
                    'status' => 200,
                    'message' => 'Información Actualizada',
                    'solicitudes' => $solicitudes,
                ]);

            }
        }

    }

    public function obtener_solicitudes($tripSolicitud = '')
    {

        if($tripSolicitud != null){

            $buscar_solicitud = SolicitudUnidades::where('trip', $tripSolicitud)->first();

            if(empty($buscar_solicitud)){
                return response([
                    'status' => 404,
                    'message' => 'No se Encuentra datos'
                ]);
            }

            $solicitud = SolicitudUnidades::select('ope_solicitud_unidad.trip', 'ope_origen.id as id_origen', 'ope_origen.origen', 'ope_tipo_unidad.id as id_tipo_unidad', 'ope_tipo_unidad.tipo_unidad', 'ope_solicitud_unidad.fecha_cedis', 'ope_solicitud_unidad.fecha_descarga' )
            ->join('ope_nombre_cliente', 'ope_nombre_cliente.id', '=', 'ope_solicitud_unidad.id_nombre_cliente')
            ->join('ope_origen', 'ope_origen.id', '=', 'ope_solicitud_unidad.id_origen')
            ->join('ope_tipo_unidad', 'ope_tipo_unidad.id', '=', 'ope_solicitud_unidad.id_tipo_unidad')
            ->join('ope_custodia_unidad', 'ope_custodia_unidad.id', '=', 'ope_solicitud_unidad.id_custodia')
            ->where('trip', $tripSolicitud)
            ->first();

            $solicitud_editar = SolicitudUnidades::select('ope_solicitud_unidad.id', 'ope_solicitud_unidad.email_solicitud','ope_nombre_cliente.nombre_cliente','ope_nombre_cliente.id as id_nombre_cliente','ope_solicitud_unidad.trip', 'ope_origen.id as id_origen', 'ope_origen.origen', 'ope_solicitud_unidad.cantidad_retirar','ope_solicitud_unidad.unidad_medida','ope_solicitud_unidad.peso_carga','ope_solicitud_unidad.cantidad_unidades','ope_tipo_unidad.id as id_tipo_unidad', 'ope_tipo_unidad.tipo_unidad', 'ope_solicitud_unidad.fecha_cedis','ope_solicitud_unidad.hora_cedis', 'ope_solicitud_unidad.fecha_descarga', 'ope_solicitud_unidad.hora_descarga', 'ope_solicitud_unidad.cliente_entrega', 'ope_solicitud_unidad.direccion_entrega', 'ope_solicitud_unidad.folio_st', 'ope_solicitud_unidad.factura','ope_solicitud_unidad.maniobras', 'ope_solicitud_unidad.cantidad_maniobras', 'ope_custodia_unidad.tipo_custodia', 'ope_custodia_unidad.id as id_custodia', 'ope_solicitud_unidad.observacion',)
            ->join('ope_nombre_cliente', 'ope_nombre_cliente.id', '=', 'ope_solicitud_unidad.id_nombre_cliente')
            ->join('ope_origen', 'ope_origen.id', '=', 'ope_solicitud_unidad.id_origen')
            ->join('ope_tipo_unidad', 'ope_tipo_unidad.id', '=', 'ope_solicitud_unidad.id_tipo_unidad')
            ->join('ope_custodia_unidad', 'ope_custodia_unidad.id', '=', 'ope_solicitud_unidad.id_custodia')
            ->where('trip', $tripSolicitud)
            ->first();


            return response(['status' => 200, 'solicitud' => $solicitud, 'solicitud_editar' => $solicitud_editar]);

        }

        $solicitudes = SolicitudUnidades::select('ope_solicitud_unidad.id', 'ope_solicitud_unidad.trip', 'ope_solicitud_unidad.email_solicitud', 'ope_nombre_cliente.nombre_cliente', 'ope_origen.origen', 'ope_solicitud_unidad.cantidad_retirar', 'ope_solicitud_unidad.unidad_medida', 'ope_solicitud_unidad.peso_carga', 'ope_tipo_unidad.tipo_unidad', 'ope_solicitud_unidad.cantidad_unidades', 'ope_solicitud_unidad.fecha_cedis', 'ope_solicitud_unidad.hora_cedis', 'ope_solicitud_unidad.cliente_entrega', 'ope_solicitud_unidad.direccion_entrega', 'ope_solicitud_unidad.fecha_descarga', 'ope_solicitud_unidad.hora_descarga', 'ope_solicitud_unidad.folio_st', 'ope_solicitud_unidad.factura', 'ope_solicitud_unidad.maniobras', 'ope_solicitud_unidad.cantidad_maniobras', 'ope_custodia_unidad.tipo_custodia', 'ope_solicitud_unidad.observacion', 'ope_solicitud_unidad.estatus' )
        ->join('ope_nombre_cliente', 'ope_nombre_cliente.id', '=', 'ope_solicitud_unidad.id_nombre_cliente')
        ->join('ope_origen', 'ope_origen.id', '=', 'ope_solicitud_unidad.id_origen')
        ->join('ope_tipo_unidad', 'ope_tipo_unidad.id', '=', 'ope_solicitud_unidad.id_tipo_unidad')
        ->join('ope_custodia_unidad', 'ope_custodia_unidad.id', '=', 'ope_solicitud_unidad.id_custodia')
        ->where('ope_solicitud_unidad.estatus', 1)
        ->orderBy('ope_solicitud_unidad.id')
        ->get();

        $solicitudes_excel = SolicitudUnidades::select('ope_solicitud_unidad.id', 'ope_solicitud_unidad.trip', 'ope_solicitud_unidad.email_solicitud', 'ope_nombre_cliente.nombre_cliente', 'ope_origen.origen', 'ope_solicitud_unidad.cantidad_retirar', 'ope_solicitud_unidad.unidad_medida', 'ope_solicitud_unidad.peso_carga', 'ope_tipo_unidad.tipo_unidad', 'ope_solicitud_unidad.cantidad_unidades', 'ope_solicitud_unidad.fecha_cedis', 'ope_solicitud_unidad.hora_cedis', 'ope_solicitud_unidad.cliente_entrega', 'ope_solicitud_unidad.direccion_entrega', 'ope_solicitud_unidad.fecha_descarga', 'ope_solicitud_unidad.hora_descarga', 'ope_solicitud_unidad.folio_st', 'ope_solicitud_unidad.factura', 'ope_solicitud_unidad.maniobras', 'ope_solicitud_unidad.cantidad_maniobras', 'ope_custodia_unidad.tipo_custodia', 'ope_solicitud_unidad.observacion', 'ope_solicitud_unidad.estatus' )
        ->join('ope_nombre_cliente', 'ope_nombre_cliente.id', '=', 'ope_solicitud_unidad.id_nombre_cliente')
        ->join('ope_origen', 'ope_origen.id', '=', 'ope_solicitud_unidad.id_origen')
        ->join('ope_tipo_unidad', 'ope_tipo_unidad.id', '=', 'ope_solicitud_unidad.id_tipo_unidad')
        ->join('ope_custodia_unidad', 'ope_custodia_unidad.id', '=', 'ope_solicitud_unidad.id_custodia')
        ->orderBy('ope_solicitud_unidad.id')
        ->get();

        return response(['status' => 200, 'solicitudes' => $solicitudes, 'solicitudes_excel' => $solicitudes_excel], 200);
    }

}
