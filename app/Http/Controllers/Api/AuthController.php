<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'registro']]);
    }

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
            $user = User::select('id', 'usuario', 'password')->where('usuario', $usuario)->first();

            if (empty($user)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Usuario y/o Password Erroneos.'
                ], 404);
            }

            $passwordValidado = Hash::check($password, $user->password);

            if (!$passwordValidado) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Usuario y/o Password Erroneos.'
                ], 404);
            }

            $token = auth()->login($user);

            return response()->json([
                'usuario' => $user,
                'token' => $token,
                'token_type' => 'bearer',
            ], 200);
        }

        return response()->json([

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
                'administrador' => ['required', 'numeric']

            ]);


            $datos = $request->only('usuario', 'nombre', 'apellidos', 'password', 'administrador');


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
                'administrador' => $datos(['administrador'])
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

    public function logout()
    {
        auth()->logout();

        return response()->json([
            'status' => 'success',
            'message' => "Cierre de sesion exitosa"
        ], 200);
    }

    public function informacion_usuario()
    {
        return response()->json(auth()->user());
    }
}
