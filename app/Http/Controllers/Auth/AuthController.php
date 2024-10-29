<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Auth;
use Namshi\JOSE\JWT;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{

    public function index()
    {
        return view('login');
    }
    public function login(Request $request)
    {
        /* $request->validate([
            'usuario' => 'required|string',
            'pass' => 'required|string',
        ]);

        // Intentar autenticación con los campos 'usuario' y 'pass'
        $credentials = $request->only('usuario', 'pass'); */

        $usuario = $request->input('usuario');
        $pass = $request->input('pass');

     /*    $usuario = "asilva";
        $pass = '2226'; */
        $user = User::where('usuario', $usuario)->first();

        if (!$user || $pass != $user->pass) {
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }
        /* print_r($user);
 */
        $token = auth('api')->login($user);

        // Retornar el token si la autenticación es exitosa
        return $this->respondWithToken($token);
    }


    public function login2(Request $request){


    $usuario = $request->input('usuario');
    $pass = $request->input('pass');

 /*    $usuario = "asilva";
    $pass = '2226'; */
    $user = User::where('usuario', $usuario)->first();

    if (!$user || $pass != $user->pass) {
        return response()->json(['error' => 'Credenciales inválidas'], 401);
    }
    /* print_r($user);
*/
    /* $token = auth('api')->login($user);

    // Retornar el token si la autenticación es exitosa
   */
    $token =JWTAuth::fromUser($user);
    return $this->respondWithToken($token);
    }

    // Método para retornar el token con algunos datos adicionales
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',

        ]);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }
}
