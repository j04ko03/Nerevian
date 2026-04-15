<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\notification;
use App\Models\solicitud;
use Illuminate\Http\JsonResponse;

class TrackingController extends Controller
{
    public function show($solicitud_id): JsonResponse
    {
        $solicitud = solicitud::delClienteActual()
            ->with(['port_origen.ciutat', 'port_desti.ciutat', 'tipus_carrega', 'tipus_contenidor'])
            ->findOrFail($solicitud_id);

        $tracking = notification::with(['incoterm'])
            ->where('solicitud_id', $solicitud->id)
            ->orderBy('date_update', 'asc')
            ->get();

        $portOrigen = $solicitud->port_origen;
        $portDesti  = $solicitud->port_desti;

        return response()->json([
            'status' => 'success',
            'data' => [
                'solicitud' => [
                    'id'               => $solicitud->id,
                    'ref'              => 'SOL-' . str_pad($solicitud->id, 4, '0', STR_PAD_LEFT),
                    'origen'           => $portOrigen ? $portOrigen->nom . ($portOrigen->ciutat ? ' (' . $portOrigen->ciutat->nom . ')' : '') : 'N/A',
                    'desti'            => $portDesti  ? $portDesti->nom  . ($portDesti->ciutat  ? ' (' . $portDesti->ciutat->nom  . ')' : '') : 'N/A',
                    'tipus_carrega'    => optional($solicitud->tipus_carrega)->nom,
                    'tipus_contenidor' => optional($solicitud->tipus_contenidor)->nom,
                    'pes_brut'         => $solicitud->pes_brut ? number_format($solicitud->pes_brut, 0, ',', '.') . ' kg' : null,
                    'estat'            => $solicitud->estat_solicitud_id,
                ],
                'historial' => $tracking,
            ]
        ]);
    }
}