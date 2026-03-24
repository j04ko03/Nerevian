<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\usuaris;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'correu'      => 'required|email',
            'contrasenya' => 'required|string',
        ]);

        $user = usuaris::with('rols')->where('correu', $request->correu)->first();

        if (!$user || !Hash::check($request->contrasenya, $user->contrasenya)) {
            return response()->json([
                'message' => 'Credenciales incorrectes.'
            ], 401);
        }

        // Eliminar tokens anteriors (una sessió activa per usuari)
        $user->tokens()->delete();

        // Crear el token de Sanctum
        $token = $user->createToken('nerevian-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => new UserResource($user),
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sessió tancada.'], 200);
    }

    public function me(Request $request)
    {
        return new UserResource($request->user()->load('rols'));
    }
}
