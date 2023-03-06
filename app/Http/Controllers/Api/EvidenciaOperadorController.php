<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EvidenciasOperadores\EvidenciaOperador;
use App\Models\EvidenciasOperadores\Unidades;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EvidenciaOperadorController extends Controller
{
    public function __construct()
    {
        /*  $this->middleware('auth:api', ['except' => []]); */
    }

    public function nuevo_registro(Request $request)
    {

        $request->validate([
            'id_usuario' => ['required', 'string'],
            'id_unidad' => ['required'],
            'odometro' => ['required', 'numeric'],
            'archivo_odometro' => ['required', 'image', 'mimes:jpg,png,jpeg,gif,svg'],
            'litros' => ['required', 'numeric'],
            'archivo_ticket' => ['required', 'image', 'mimes:jpg,png,jpeg,gif,svg'],
        ]);

        $id_usuario = $request->input('id_usuario');
        $no_economico = $request->input('id_unidad');
        $odometro = $request->input('odometro');
        $litros = $request->input('litros');

        $archivo_ticket = $request->file('archivo_ticket');
        $archivo_odometro = $request->file('archivo_odometro');

        $nombre_archivo_odo = date('dmY_His') . '_' . $archivo_odometro->getClientOriginalName();
        $archivo_odometro->move(public_path('evidencia'), $nombre_archivo_odo);

        $nombre_archivo_tic = date('dmY_His') . '_' . $archivo_ticket->getClientOriginalName();
        $archivo_ticket->move(public_path('evidencia'), $nombre_archivo_tic);

        $registro = EvidenciaOperador::create([

            'id_usuario' => $id_usuario,
            'id_unidad' => $no_economico,
            'odometro' => $odometro,
            'archivo_odometro' => $nombre_archivo_odo,
            'litros' => $litros,
            'archivo_ticket' => $nombre_archivo_tic,

        ]);

        return response()->json([
            'registro' => $registro,
        ]);
    }

    public function unidades()
    {

        $unidades = Unidades::all();

        return response(['unidades' => $unidades], 200);
    }

    public function buscar_todos()
    {

        $busqueda = EvidenciaOperador::select('ope_evidencia_operadores.id', 'usuarios.usuario', DB::raw('concat(usuarios.nombre, " ", usuarios.apellidos) as nombre'), 'ope_unidades.numero_economico as no_economico', 'ope_unidades.placas', 'ope_evidencia_operadores.odometro', 'ope_evidencia_operadores.litros', 'ope_evidencia_operadores.archivo_odometro', 'ope_evidencia_operadores.archivo_ticket', DB::raw('date_format(ope_evidencia_operadores.fecha_registro, "%Y-%m-%d %H:%i:%s") as fecha_registro'))
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

        ], 200);
    }

    public function buscar_registro($idEvidencia)
    {
        $buscar_usuario = EvidenciaOperador::select('ope_evidencia_operadores.id', 'usuarios.usuario', DB::raw('concat(usuarios.nombre, " ", usuarios.apellidos) as nombre'), 'ope_unidades.numero_economico as no_economico', 'ope_unidades.placas', 'ope_evidencia_operadores.odometro', 'ope_evidencia_operadores.litros', 'ope_evidencia_operadores.archivo_odometro', 'ope_evidencia_operadores.archivo_ticket', DB::raw('date_format(ope_evidencia_operadores.fecha_registro, "%Y-%m-%d") as fecha_registro'))
            ->join('ope_unidades', 'ope_unidades.id', '=', 'ope_evidencia_operadores.id_unidad')
            ->join('usuarios', 'usuarios.id', '=', 'ope_evidencia_operadores.id_usuario')
            ->where('ope_evidencia_operadores.id', $idEvidencia)
            ->first();


        if ($buscar_usuario) {

            $registro = [
                'id' => $buscar_usuario->id,
                'nombre' => $buscar_usuario->nombre,
                'no_economico' => $buscar_usuario->no_economico,
                'placas' => $buscar_usuario->placas,
                'odometro' => $buscar_usuario->odometro,
                'archivo_odometro' => asset('/evidencia/') . "/" . $buscar_usuario->archivo_odometro,
                'litros' => $buscar_usuario->litros,
                'archivo_ticket' => asset('/evidencia/') . "/" . $buscar_usuario->archivo_ticket,
                'fecha_registro' => $buscar_usuario->fecha_registro,
            ];

            return response()->json([
                'status' => 'success',
                'registro' => $registro,
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Dato no encontrado'
        ], 404);
    }

    public function eliminar_registro($idEvidencia)
    {
        //Validar que exista registro
        $validar = EvidenciaOperador::where('id', $idEvidencia)->first();

        if (empty($validar)) {

            return response([
                'status' => '404',
                'message' => 'Dato no encontrado'
            ], 404);
        }

        //Eliminar registro
        $validar->delete();

        $busqueda = EvidenciaOperador::select('ope_evidencia_operadores.id', 'usuarios.usuario', DB::raw('concat(usuarios.nombre, " ", usuarios.apellidos) as nombre'), 'ope_unidades.numero_economico as no_economico', 'ope_unidades.placas', 'ope_evidencia_operadores.odometro', 'ope_evidencia_operadores.litros', 'ope_evidencia_operadores.archivo_odometro', 'ope_evidencia_operadores.archivo_ticket', DB::raw('date_format(ope_evidencia_operadores.fecha_registro, "%Y-%m-%d %H:%i:%s") as fecha_registro'))
            ->join('ope_unidades', 'ope_unidades.id', '=', 'ope_evidencia_operadores.id_unidad')
            ->join('usuarios', 'usuarios.id', '=', 'ope_evidencia_operadores.id_usuario')
            ->get();

        return response()->json([
            'status' => '200',
            'message' => 'Registro eliminado',
            'datos' => $busqueda
        ], 200);
    }

    public function buscar_fecha(Request $request)

    {

        $request->validate([
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date']
        ]);

        $fecha_inicio = $request->input('fecha_inicio');
        $fecha_fin = $request->input('fecha_fin');

        if ($fecha_fin < $fecha_inicio) {

            return response([
                'status' => '500',
                'message' => 'La fecha final no puede ser mayor a la inicial'
            ], 500);
        }

        $buscar_fecha = EvidenciaOperador::select('ope_evidencia_operadores.id', 'usuarios.usuario', DB::raw('concat(usuarios.nombre, " ", usuarios.apellidos) as nombre'), 'ope_unidades.numero_economico as no_economico', 'ope_unidades.placas', 'ope_evidencia_operadores.odometro', 'ope_evidencia_operadores.litros', 'ope_evidencia_operadores.archivo_odometro', 'ope_evidencia_operadores.archivo_ticket', DB::raw('date_format(ope_evidencia_operadores.fecha_registro, "%Y-%m-%d") as fecha_registro'))
            ->join('ope_unidades', 'ope_unidades.id', '=', 'ope_evidencia_operadores.id_unidad')
            ->join('usuarios', 'usuarios.id', '=', 'ope_evidencia_operadores.id_usuario')
            ->whereBetween(DB::raw('date_format(ope_evidencia_operadores.fecha_registro, "%Y-%m-%d")'), [$fecha_inicio, $fecha_fin])
            ->get();

        if (!$buscar_fecha->isEmpty()) {

            $registros = [];

            for ($i = 0; $i < count($buscar_fecha); $i++) {

                $datos = [
                    'id' => $buscar_fecha[$i]->id,
                    'nombre' => $buscar_fecha[$i]->nombre,
                    'no_economico' => $buscar_fecha[$i]->no_economico,
                    'placas' => $buscar_fecha[$i]->placas,
                    'odometro' => $buscar_fecha[$i]->odometro,
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
