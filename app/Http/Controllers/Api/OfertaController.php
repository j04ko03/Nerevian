<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ofertes;
use App\Models\operacions;
use App\Models\solicitud;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OfertaController extends Controller
{
    // POST /api/solicitudes/{solicitud}/ofertes/{oferta}/acceptar
    public function acceptar(Request $request, $solicitudId, $ofertaId): JsonResponse
    {
        $clientId = auth()->user()->clients->id;
        $sol = solicitud::where('client_id', $clientId)->findOrFail($solicitudId);
        $oferta = ofertes::where('solicitud_id', $sol->id)->findOrFail($ofertaId);

        $oferta->update(['estat_oferta_id' => 1]); // aceptada

        $operacio = operacions::create([
            'oferta_id' => $oferta->id,
            'client_id' => $clientId,
            'operador_id' => $sol->operador_id,
            'estat_id' => 1, // en curso
            'codi_referencia' => 'OP-' . str_pad($oferta->id, 5, '0', STR_PAD_LEFT),
            'data_inici' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "Oferta acceptada. Operació creada.",
            'data' => $operacio,
        ], 201);
    }

    // POST /api/solicitudes/{solicitud}/ofertes/{oferta}/rebutjar
    public function rebutjar(Request $request, $solicitudId, $ofertaId): JsonResponse
    {
        $clientId = auth()->user()->clients->id;
        $sol = solicitud::where('client_id', $clientId)->findOrFail($solicitudId);
        $oferta = ofertes::where('solicitud_id', $sol->id)->findOrFail($ofertaId);

        $oferta->update([
            'estat_oferta_id' => 4, // rechazada
            'deny_reason' => $request->input('motiu'),
        ]);
        $sol->update(['estat_solicitud_id' => 2]); // torna a en_revision

        return response()->json([
            'status' => 'success',
            'message' => 'Oferta rebutjada. Espereu una nova cotització.',
        ]);
    }

    // POST /api/solicitudes/{solicitud}/contraoferta
    public function contraoferta(Request $request, $solicitudId): JsonResponse
    {
        $clientId = auth()->user()->clients->id;
        $sol = solicitud::where('client_id', $clientId)->findOrFail($solicitudId);

        $validated = $request->validate([
            'pressupost' => 'required|numeric|min:0',
            'temps_estimat' => 'nullable|integer|min:1',
            'comentaris' => 'nullable|string',
        ]);

        // Mark current sent offers as rejected before sending counter-offer
        ofertes::where('solicitud_id', $sol->id)
            ->where('estat_oferta_id', 2) // revisión
            ->update(['estat_oferta_id' => 4]); //negociación

        ofertes::create([
            'solicitud_id' => $sol->id,
            'clients_id' => $clientId,
            'pressupost' => $validated['pressupost'],
            'comentaris' => $validated['comentaris'] ?? null,
            'estat_oferta_id' => 2, // enviada (al operador per revisar)
            'es_contraoferta' => true,
            'data_creacio' => now()->toDateString(),
            'data_validessa_inicial' => now()->toDateString(),
            'data_validessa_final' => now()->addDays(30)->toDateString(),
            'moneda' => 'EUR',
        ]);

        $sol->update(['estat_solicitud_id' => 4]); // en_negociacion

        return response()->json([
            'status' => 'success',
            'message' => 'Contraoferta enviada correctament.',
        ], 201);
    }
}