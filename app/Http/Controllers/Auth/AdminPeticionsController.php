<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\peticions_registre;
use App\Models\usuaris;
use App\Http\Resources\PeticioRegistreResource;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

// VISUALIZACIÓN DE PETICIONES PARA ADMIN
class AdminPeticionsController extends Controller
{
    /**
     * Display a listing of pending registration requests.
     */
    public function index()
    {
        $requests = peticions_registre::whereIn('estat', ['0', 0])
            ->orderBy('data_creacio', 'desc')
            ->get();

        return PeticioRegistreResource::collection($requests);
    }

    /**
     * Approve the registration request and create a user.
     */
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
            'rol_id' => $peticion->rol_id, // Rol que ha pedido el usuario en la petición de registo
        ]);

        // Update la petición de la base de datos tras la aprobación
        // $peticion->update([
        //     'estat' => '1',
        //     'resolt_per' => $request->user()->id,
        //     'data_resolucio' => now(),
        // ]);

        // Delete la petición de la base de datos tras la aprobación
        $peticion->delete();

        return response()->json([
            'message' => 'Petición aprobada y usuario creado correctamente.',
            'user_id' => $user->id
        ]);
    }

    /**
     * Reject the registration request.
     */
    public function reject(Request $request, $id)
    {
        $peticion = peticions_registre::findOrFail($id);

        if ($peticion->estat !== '0') {
            return response()->json(['message' => 'Esta petición ya ha sido resuelta.'], 400);
        }

        // Update la petición de la base de datos tras la rechazación.
        // $peticion->update([
        //     'estat' => '1',
        //     'rao_rebuig' => $request->rao_rebuig,
        //     'resolt_per' => $request->user()->id,
        //     'data_resolucio' => now(),
        // ]);

        // Delete la petición de la base de datos tras la rechazación.
        $peticion->delete();

        return response()->json(['message' => 'Petición rechazada y eliminada correctamente.']);
    }
}
