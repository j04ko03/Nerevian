<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Solicitud;
use App\Models\usuaris;

class SolicitudController
{
    /**
     * Display a listing of the resource.
     * Para mostrar la lista de solicitudes
     */
    public function index(): JsonResponse
    {
        // Obtener solo las solicitudes del cliente logueado
        // Uso auth porque Laravel al recibir un token (bearer) 
        // desde el front (stores/authStore.js) sabe qué usuario esta logeado.
        $clientId = auth()->user()->clients->id ?? null;

        $solicitudes = Solicitud::with(['estat_solicitud', 'tipus_transport', 'port_origen', 'port_desti', 'incoterm'])
            ->delClienteActual() // queryscope de solicitud.php
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $solicitudes
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * Para recibir nuevas solicitudes desde el front
     */
    public function store(Request $request): JsonResponse
    {
        // Valido lo que viene del front.
        // De normal, cuando se usa una estructura monolítica de Laravel sin SPA ni nada así, 
        // si fallaba el $validated = $request->validate([...]); se recargaba la página y devolvía 
        // el formulario. Pero en este caso, que como estoy viendo, también se usa para la estructura 
        // de este proyecto. La única diferencia es que si la validación falla 
        // ($validated = $request->validate([...]);) no se recarga nada y se detiene el código.
        $validated = $request->validate([
            'tipus_transport_id' => 'required|exists:tipus_transports,id',
            'tipus_fluxe_id' => 'required|exists:tipus_fluxes,id',
            'tipus_carrega_id' => 'required|exists:tipus_carrega,id',
            'incoterm_id' => 'required|exists:Incoterm,id',
            'port_origen_id' => 'required|exists:ports,id',
            'port_desti_id' => 'required|exists:ports,id',
            'pes_brut' => 'required|numeric',
            'volum' => 'nullable|numeric',
            'comentaris' => 'nullable|string',
            'tipus_contenidor_id' => 'nullable|exists:tipus_contenidors,id',
            'tipus_validacio_id' => 'nullable|exists:tipus_validacions,id'
        ]);

        // Digo x cliente logueado y su estado por defecto
        // Más que nada para forzar campos internos de la solicitud y el usuario no pueda elegirlos. (security stuff)
        // Hablando de seguridad, por eso también en $validated he puesto campos como exists:tipus_transports,id, para 
        // que el usuario no pueda ingresar valores que no existen en la base de datos saltandose el front.
        $validated['client_id'] = auth()->user()->clients->id;
        $validated['estats_solicitud_id'] = 1; // nueva

        $solicitiud = solicitud::create($validated);

        return response()->json([
            'status' => 'succes',
            'message' => 'Soli creada',
            'data' => $solicitiud
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $solicitud = Solicitud::with([
            'estat_solicitud',
            'tipus_transport',
            'port_origen',
            'port_desti',
            'incoterm',
            'documents'
        ])
            ->delClienteActual() // queryscope de solicitud.php. Si no es ClienteActual ->findOrFail() lanzará un 404 de manual.
            ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $solicitud
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $solicitud = Solicitud::delClienteActual()->findOrFail($id);

        $validated = $request->validate([
            'pes_brut' => 'sometimes|numeric',
            'volum' => 'sometimes|numeric',
            'comentaris' => 'sometimes|string',
        ]);

        $solicitud->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Solicitud actualizada',
            'data' => $solicitud
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $solicitud = Solicitud::delClienteActual()->findOrFail($id);
        $solicitud->delete();

        return response()->json(['status' => 'success', 'message' => 'Eliminazao realizada (de soli)']);
    }
}
