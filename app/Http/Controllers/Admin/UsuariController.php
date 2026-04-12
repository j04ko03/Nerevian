<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Classes\Utilitat;
use App\Models\usuaris;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuariController extends Controller
{
    // GET /api/users
    public function index()
    {
        try {
        $usuaris = usuaris::with('rols')->get();
        return response()->json($usuaris, 200);
        }
        catch (QueryException $e) {
            $missatge = Utilitat::errorMessage($e);
            return response()->json(['error' => 'No se han podido obtener los datos' . ' - ' . $missatge], 500);
        }
    }

    // GET /api/users/{id}
    public function show(usuaris $usuario) {
        $usuario->load('rol');
        return response()->json($usuario, 200);
    }

    // PUT/PATCH /api/users/{id}
    public function update(Request $request, usuaris $usuario)
        {
            $request->validate([
                'correu' => 'required|email|max:150|unique:usuarios,correu,' . $usuario->id,
                'nom' => 'required|max:50',
                'cognoms' => 'nullable|max:50',
                'telefon' => 'nullable|max:20',
                'rol_id' => 'required|integer|exists:roles,id',
            ]);

            $data = $request->except(['contrasenya']);

            // Solo actualizar contraseña si se envía una nueva
            if ($request->filled('contrasenya')) {
                $request->validate(['contrasenya' => 'min:8|max:256']);
                $data['contrasenya'] = Hash::make($request->contrasenya);
            }

            $usuario->update($data);

            return response()->json([
                'message' => 'Usuario actualizado correctamente',
                'data' => $usuario
            ], 200);
    }
  
    // DELETE /api/users/{id}
    public function destroy(usuaris $usuario)
    {
        try {
            $usuario->delete();
            return response()->json(['message' => 'Usuario eliminado correctamente'], 200);
        }
        catch (QueryException $e) {
            $missatge = Utilitat::errorMessage($e);
            return response()->json(['error' => 'No se ha podido eliminar el usuario' . ' - ' . $missatge], 500);
        }
    }
}