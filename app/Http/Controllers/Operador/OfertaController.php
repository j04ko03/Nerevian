<?php

namespace App\Http\Controllers\Operador;

use App\Http\Controllers\Controller;
use App\Models\ofertes;
use App\Models\solicitud;
use App\Models\operacions;
use Illuminate\Http\Request;

class OfertaController extends Controller
{
    // POST /api/operador/solicituds/{solicitud}/ofertes/{oferta}/acceptar
    // Accepta la contraoferta del client i crea l'operació
    public function acceptar(Request $request, $solicitudId, $ofertaId)
    {
        $sol = solicitud::findOrFail($solicitudId);
        $oferta = ofertes::where('solicitud_id', $sol->id)
            ->where('es_contraoferta', true)
            ->findOrFail($ofertaId);

        $oferta->update(['estat_oferta_id' => 1]); // aceptada

        $operacio = operacions::create([
            'oferta_id' => $oferta->id,
            'client_id' => $sol->client_id,
            'operador_id' => auth()->id(),
            'estat_id' => 1,
            'codi_referencia' => 'OP-' . str_pad($oferta->id, 5, '0', STR_PAD_LEFT),
            'data_inici' => now(),
        ]);

        $sol->update(['estat_solicitud_id' => 3]); // torna a cotizada (amb oferta acceptada)

        return response()->json([
            'status' => 'success',
            'message' => 'Contraoferta acceptada. Operació creada.',
            'data' => $operacio,
        ], 201);
    }

    // POST /api/operador/solicituds/{id}/oferta
    public function store(Request $request, $solicitudId)
    {
        $sol = solicitud::findOrFail($solicitudId);

        $validated = $request->validate([
            'pressupost' => 'required|numeric|min:0',
            'comentaris' => 'nullable|string',
            'data_validessa_inicial' => 'nullable|date',
            'data_validessa_final' => 'nullable|date',
            'moneda' => 'nullable|string|max:10',
        ]);

        // Reject any previous sent or pending offers before sending a new one
        ofertes::where('solicitud_id', $solicitudId)
            ->where('estat_oferta_id', 2)
            ->update(['estat_oferta_id' => 4]);

        $oferta = ofertes::create([
            'solicitud_id' => $solicitudId,
            'clients_id' => $sol->client_id,
            'pressupost' => $validated['pressupost'],
            'comentaris' => $validated['comentaris'] ?? null,
            'estat_oferta_id' => 2, // enviada
            'data_creacio' => now()->toDateString(),
            'data_validessa_inicial' => $validated['data_validessa_inicial'] ?? now()->toDateString(),
            'data_validessa_final' => $validated['data_validessa_final'] ?? now()->addDays(30)->toDateString(),
            'moneda' => $validated['moneda'] ?? 'EUR',
        ]);

        $sol->update(['estat_solicitud_id' => 3]); // cotizada

        return response()->json([
            'status' => 'success',
            'message' => 'Oferta enviada correctament',
            'data' => $oferta,
        ], 201);
    }
}