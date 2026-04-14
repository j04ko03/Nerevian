<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Clases\Utilitat;
use App\Models\usuaris;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuariController extends Controller
{
    // GET /api/users
    public function index()
    {
        try {
            $usuaris = usuaris::with('rols')->get();
            return response()->json($usuaris, 200);
        } catch (QueryException $e) {
            $missatge = Utilitat::errorMessage($e);
            return response()->json(['error' => 'No se han podido obtener los datos - ' . $missatge], 500);
        }
    }

    // GET /api/users/{user}
    public function show(usuaris $user)
    {
        $user->load('rols');
        return response()->json($user, 200);
    }

    // PUT/PATCH /api/users/{user}
    public function update(Request $request, $user)
    {
        $user = usuaris::findOrFail($user);

        $request->validate([
            'correu'   => ['required', 'email', 'max:150', Rule::unique('usuaris', 'correu')->ignore($user->id)],
            'nom'      => 'required|max:50',
            'cognoms'  => 'nullable|max:50',
            'telefon'  => 'nullable|max:20',
            'rol_id'   => 'required|integer|exists:rols,id',
        ]);

        $fields = ['correu', 'nom', 'cognoms', 'telefon', 'rol_id'];
        $data = [];

        foreach ($fields as $field) {
            $incoming = $request->input($field);
            if ($incoming !== null && $incoming !== $user->$field) {
                $data[$field] = $incoming;
            }
        }

        if ($request->filled('contrasenya')) {
            $request->validate(['contrasenya' => 'min:8|max:256']);
            $data['contrasenya'] = Hash::make($request->contrasenya);
        }

        if (!empty($data)) {
            $user->update($data);
        }

        return response()->json([
            'message' => 'Usuario actualizado correctamente',
            'data'    => $user->fresh('rols'),
        ], 200);
    }

    // DELETE /api/users/{user}
    public function destroy(usuaris $user)
    {
        $user->delete();
        return response()->json(['message' => 'Usuario desactivat correctament'], 200);
    }
}