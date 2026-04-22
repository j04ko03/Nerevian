<?php

namespace App\Http\Controllers\Operador;

use App\Http\Controllers\Controller;
use App\Models\clients;
use App\Models\dni;
use App\Models\usuaris;
use Illuminate\Http\Request;

class ClientsController extends Controller
{
    // GET /api/operador/clients
    // Retorna tots els usuaris amb rol client (actius i pendents)
    public function index()
    {
        $usuaris = usuaris::with(['clients.dni'])
            ->where('rol_id', 2)
            ->get()
            ->map(fn($u) => [
                'id'       => $u->id,
                'nom'      => $u->nom,
                'cognoms'  => $u->cognoms,
                'correu'   => $u->correu,
                'telefon'  => $u->telefon,
                'actiu'    => (bool) $u->clients?->actiu,
                'client'   => $u->clients ? [
                    'id'               => $u->clients->id,
                    'dni'              => $u->clients->dni?->dni,
                    'solicituds_count' => $u->clients->solicituds()->count(),
                ] : null,
            ]);

        return response()->json($usuaris, 200);
    }

    // GET /api/operador/clients/{id} — id = clients.id
    public function show($id)
    {
        $client = clients::with(['usuaris', 'dni'])
            ->withCount('solicituds')
            ->findOrFail($id);

        return response()->json($client, 200);
    }

    // POST /api/operador/clients — donar d'alta un usuari com a client
    public function store(Request $request)
    {
        $request->validate([
            'usuari_id' => 'required|integer|exists:usuaris,id',
        ]);

        $client = clients::updateOrCreate(
            ['usuari_id' => $request->input('usuari_id')],
            ['actiu' => 1],
        );

        return response()->json(
            $client->load(['usuaris', 'dni'])->loadCount('solicituds'),
            201,
        );
    }

    // PUT /api/operador/clients/{id} — id = clients.id
    public function update(Request $request, $id)
    {
        $client = clients::with(['usuaris', 'dni'])->findOrFail($id);

        $request->validate([
            'nom'     => 'nullable|string|max:50',
            'cognoms' => 'nullable|string|max:50',
            'telefon' => 'nullable|string|max:20',
            'dni'     => 'nullable|string|max:20',
        ]);

        if ($client->usuaris) {
            $usuariData = array_filter([
                'nom'     => $request->input('nom'),
                'cognoms' => $request->input('cognoms'),
                'telefon' => $request->input('telefon'),
            ], fn($v) => $v !== null);

            if (!empty($usuariData)) {
                $client->usuaris->update($usuariData);
            }
        }

        if ($request->filled('dni') && $client->dni) {
            $client->dni->update(['dni' => $request->input('dni')]);
        }

        return response()->json([
            'message' => 'Client actualitzat correctament',
            'data'    => $client->fresh(['usuaris', 'dni'])->loadCount('solicituds'),
        ], 200);
    }

    // DELETE /api/operador/clients/{id} — id = clients.id
    public function destroy($id)
    {
        $client = clients::findOrFail($id);
        $client->delete();

        return response()->json(['message' => 'Client eliminat correctament'], 200);
    }
}