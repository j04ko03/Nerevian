<?php

namespace App\Http\Controllers\Operador;

use App\Http\Controllers\Controller;
use App\Models\solicitud;

class SolicitudController extends Controller
{
    // GET /api/operador/solicituds
    // Retorna les sol·licituds assignades a l'operador autenticat
    public function index()
    {
        $operadorId = auth()->id();

        $solicituds = solicitud::with([
                'client.usuaris',
                'port_origen.ciutat',
                'port_desti.ciutat',
                'estat_solicitud',
                'tipus_transport',
                'incoterm',
                'ofertes.estat',
            ])
            ->where('operador_id', $operadorId)
            ->orderBy('data_creacio', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $solicituds,
        ]);
    }
}