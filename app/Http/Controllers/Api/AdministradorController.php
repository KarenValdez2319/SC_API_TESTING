<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EvidenciasOperadores\EvidenciaOperador;
use App\Models\Monitorista\BitacoraViajes;
use App\Models\Monitorista\SeguimientoViaje;
use App\Models\Vistas\Viajes_Ultimo_Estatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdministradorController extends Controller
{
    //
    public function buscar_viajes(){

        $viajes = Viajes_Ultimo_Estatus::all();

        return response(["statusCode" => 200, "viajes" => $viajes], 200);

        /*$busqueda = EvidenciaOperador::select('ope_evidencia_operadores.id', 'usuarios.usuario', DB::raw('concat(usuarios.nombre, " ", usuarios.apellidos) as nombre'), 'ope_unidades.numero_economico as no_economico', 'ope_unidades.placas', 'ope_evidencia_operadores.odometro', 'ope_evidencia_operadores.litros', 'ope_evidencia_operadores.archivo_odometro', 'ope_evidencia_operadores.archivo_ticket', DB::raw('date_format(ope_evidencia_operadores.fecha_registro, "%Y-%m-%d %H:%i:%s") as fecha_registro'))
            ->join('ope_unidades', 'ope_unidades.id', '=', 'ope_evidencia_operadores.id_unidad')
            ->join('usuarios', 'usuarios.id', '=', 'ope_evidencia_operadores.id_usuario')
            ->get();

        $registros = [];

        for ($i = 0; $i < count($busqueda); $i++) {

            $datos = [
                'id' => $busqueda[$i]->id,
                'nombre' => $busqueda[$i]->nombre,
                'no_economico' => $busqueda[$i]->no_economico,
                'placas' => $busqueda[$i]->placas,
                'odometro' => $busqueda[$i]->odometro,
                'archivo_odometro' => asset('/evidencia/') . "/" . $busqueda[$i]->archivo_odometro,
                'litros' => $busqueda[$i]->litros,
                'archivo_ticket' => asset('/evidencia/') . "/" . $busqueda[$i]->archivo_ticket,
                'fecha_registro' => $busqueda[$i]->fecha_registro,
            ];

            array_push($registros, $datos);
        }

        return response()->json([

            'status' => 'success',
            "registros" => $registros

        ], 200);*/
    }

    public function buscar_viaje($idViaje){

        $viaje = BitacoraViajes::where('id', $idViaje)->first();

        $seguimientos = SeguimientoViaje::select('ope_seguimiento_viajes.id', 'ope_seguimiento_viajes.comentarios', 'ope_estatus.descripcion as estatus', 'ope_seguimiento_viajes.fecha_registro')
                                            ->join('ope_estatus', 'ope_estatus.id', '=', 'ope_seguimiento_viajes.id_estatus')
                                            ->where('id_viaje', $idViaje)
                                            ->orderBy('ope_estatus.orden', 'asc')
                                            ->get();
                                            
        $odometroImagen = EvidenciaOperador::select('id', 'archivo_odometro')->where('id_viaje', $idViaje)->get();
        $ticketImage = EvidenciaOperador::select('id', 'archivo_ticket')->where('id_viaje', $idViaje)->get();

        $imagenesOdometro = [];
        $imagenesTicket = [];

        for ($i=0; $i < count($odometroImagen); $i++) { 
            $imagen = [
                "id" => $odometroImagen[$i]->id,
                "imagen" => asset('/evidencia').'/'.$odometroImagen[$i]->archivo_odometro,
            ];

            array_push($imagenesOdometro, $imagen);
        }

        for ($i=0; $i < count($ticketImage); $i++) { 
            $imagen = [
                "id" => $ticketImage[$i]->id + 10000,
                "imagen" => asset('/evidencia').'/'.$ticketImage[$i]->archivo_ticket,
            ];

            array_push($imagenesTicket, $imagen);
        }

        //Latitud y Longitud
        $medidas = SeguimientoViaje::select('latitud', 'longitud')->where('latitud', '<>', 0)->where('longitud', '<>', '0')->where('id_viaje', $idViaje)->get();

        return response(["seguimientos" => $seguimientos, "imagenes_odometro" => $imagenesOdometro, "imagenes_ticket" => $imagenesTicket, "medidas" => $medidas], 200);

        return response()->json([
            'status' => 'error',
            'message' => 'Dato no encontrado'
        ], 404);
    }
}
