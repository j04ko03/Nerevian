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
        // Usamos el scope para validar que la solicitud pertenece al cliente.
        $solicitud = solicitud::delClienteActual()->findOrFail($solicitud_id);

        // 
        $tracking = notification::with(['incoterm.trackingStep'])
            ->where('solicitud_id', $solicitud->id)
            ->orderBy('date_update', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'solicitud' => [
                    'id' => $solicitud->id,
                    'estat' => $solicitud->estat_solicitud_id
                ],
                'historial' => $tracking
            ]
        ]);
    }
}