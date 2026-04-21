<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Models\tipus_transports;
use App\Models\tipus_carrega;
use App\Models\tipus_fluxes;
use App\Models\tipus_contenidors;
use App\Models\tipus_validacions;
use App\Models\estats_solicituds;
use App\Models\tipus_incoterms;
use App\Models\ports;
use App\Models\tipo_documento;

class CatalogoController extends Controller
{
    // PAra que me devuelva todos los catálogos necesarios para los selects del frontend.
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'tipus_transports' => tipus_transports::all(),
                'tipus_carregues' => tipus_carrega::all(),
                'tipus_fluxes' => tipus_fluxes::all(),
                'tipus_contenidors' => tipus_contenidors::all(),
                'tipus_validacions' => tipus_validacions::all(),
                'estats_solicituds' => estats_solicituds::all(),
                'incoterms' => tipus_incoterms::all(),
                'ports' => ports::with('ciutat')->get(),
                'tipus_documents' => tipo_documento::all(),
            ]
        ]);
    }
}