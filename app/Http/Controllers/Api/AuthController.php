<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;
use Laravel\Sanctum\Sanctum;
class AuthController extends Controller
{
    public function login(Request $request)
    {

        $header = $request->header('Accept', 'application/json');

        if ($header == 'application/json') {

            $request->validate([
                'usuario' => ['required', 'string'],
                'password' => ['required', 'string', 'min:6']
            ]);

            $usuario = $request->input('usuario');
            $password = $request->input('password');

            //Validar si existe en la base de datos
            $user = User::select('id', 'usuario', 'password', 'rol')->where('usuario', $usuario)->first();

            if (empty($user)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Usuario y/o Password Incorrectos.'
                ], 404);
            }

            $passwordValidado = Hash::check($password, $user->password);

            if (!$passwordValidado) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Usuario y/o Password Incorrectos.'
                ], 404);
            }

            $token = $user->createToken('App')->plainTextToken;

            $usuario = [
                'id' => $user->id,
                'usuario' => $user->usuario,
                'rol' => $user->rol,
                'token_type' => 'bearer',
                'token' => $token,
            ];

            return response([
                "statusCode" => 200,
                'usuario' => $usuario,
            ], 200);
        }

        return response ([

            'status' => 'error',
            'message' => 'Error en peticion, Header debe contener "Key:Accept", "Value:application/json"'

        ], 500);
    }

    public function registro(Request $request)
    {

        $header = $request->header('Accept', 'application/json');

        if ($header == 'application/json') {

            $request->validate([

                'nombre' => ['required', 'string'],
                'apellidos' => ['required', 'string'],
                'usuario' => ['required', 'string'],
                'password' => ['required', 'string', 'min:6'],
                'rol' => ['required', 'string']

            ]);


            $datos = $request->only('usuario', 'nombre', 'apellidos', 'password', 'rol');


            $user = User::where('usuario', $datos['usuario'])->first();

            if ($user) {
                return response()->json([

                    'status' => 'error',
                    'messages' => 'Usuario ya existe'

                ], 400);
            }

            $usuario = User::create([
                'nombre' => $datos['nombre'],
                'apellidos' => $datos['apellidos'],
                'usuario' => $datos['usuario'],
                'password' => Hash::make($datos['password']),
                'rol' => $datos['rol']
            ]);


            return response()->json([
                'status' => 'success',
                'message' => 'Usuario Creado con Exito',
                'nuevo_usuario' => $usuario
            ], 201);
        }

        return response()->json([

            'status' => 'error',
            'message' => 'Error en peticion, Header debe contener "Key:Accept", "Value:application/json"'

        ], 500);
    }

    public function logout(Request $request)
    {
        /* $user = request()->user();

        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete(); */
/*
        $request->user()->currentAccessToken()->delete();

        $user->tokens()->where('id',$tokenId)->delete(); */

/*         $accessToken = $request->bearerToken();

        $token = SanctumPersonalAccessToken::findToken($accessToken);

        $token->delete(); */

        $request->user()->currentAccessToken()->delete();

        return response([
            'status' => 'success',
            'message' => "Cierre de sesion exitosa"
        ], 200);
    }

    public function informacion_usuario(Request $request)
    {

        $user =  $request->user();

          $usuario = [
            'id' => $user->id,
            'nombre' => $user->nombre,
            'apellidos' => $user->apellidos,
            'usuario' => $user->usuario,
            'rol' => $user->rol,
        ];

        return response(['statusCode' => 200, 'usuario' => $usuario], 200);
    }
}
