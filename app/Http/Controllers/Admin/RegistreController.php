<?php

namespace App\Http\Controllers\Admin;

use App\Clases\Utilitat;
use App\Http\Controllers\Controller;
use App\Models\peticions_registre;
use App\Models\usuaris;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;


class RegistreController extends Controller
{
    // GET /registration-requests
    public function index()
    {
        try {
        $peticions = peticions_registre::all();
        return response()->json($peticions, 200);
        }
        catch (QueryException $e) {
            $missatge = Utilitat::errorMessage($e);
            return response()->json(['error' => 'No se han podido obtener los datos' . ' - ' . $missatge], 500);
        }
    }

    // POST /registration-requests/approve/{id}
    public function approve(Request $request, $id)
    {
        $peticion = peticions_registre::findOrFail($id);

        if ($peticion->estat !== '0') {
            return response()->json(['message' => 'Esta petición ya ha sido resuelta.'], 400);
        }

        // Crear usuario automáticamente con rol 2 (Usuario normal)
        $user = usuaris::create([
            'nom' => $peticion->contacte,
            'cognoms' => '', // Evitar error de NULL en la BD
            'correu' => $peticion->correu,
            'contrasenya' => $peticion->contrasenya, // Ya está hasheada en la petición
            'telefon' => $peticion->telefon,
            'rol_id' => 2, // Rol de usuario normal
        ]);

        // Marcamos estado como aceptada
        $peticion->estat = '1';
        $peticion->save();

        return response()->json([
            'message' => 'Petición aceptada y usuario creado correctamente.',
            'user_id' => $user->id
        ]);
    }

    // POST /registration-requests/reject/{id}
    public function reject(Request $request, $id)
    {
        $peticion = peticions_registre::findOrFail($id);

        if ($peticion->estat !== '0') {
            return response()->json(['message' => 'Esta petición ya ha sido resuelta.'], 400);
        }

        // Marcamos estado como rechazada
        $peticion->estat = '2';
        $peticion->save();

        return response()->json(['message' => 'Petición rechazada correctamente.']);
    }
}