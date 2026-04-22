<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\usuaris;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

// PARA LA AUTENTICACIÓN Y ADJUDICCIÓN DE TOKENS
class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'correu' => 'required|email',
            'contrasenya' => 'required|string',
        ]);

        $user = usuaris::withTrashed()->with(['rols', 'clients'])->where('correu', $request->correu)->first();

        if (!$user || !Hash::check($request->contrasenya, $user->contrasenya)) {
            return response()->json([
                'message' => 'Credenciales incorrectas.'
            ], 401);
        }

        if ($user->trashed()) {
            return response()->json([
                'message' => 'El compte ha estat desactivat. Contacta amb l\'administrador.'
            ], 403);
        }

        // Crear el token de Sanctum
        $token = $user->createToken('nerevian-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => new UserResource($user),
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesión cerrada.'], 200);
    }

    public function me(Request $request)
    {
        return new UserResource($request->user()->load(['rols', 'clients']));
    }
}