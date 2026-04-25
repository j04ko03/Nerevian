<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Models\solicitud;

class TrackingController extends Controller
{
    public function show($id)
    {

        // --- ESTO ES SOLO PARA TEST (Bórralo luego) ---
        \Illuminate\Support\Facades\Http::fake([
            '*api/tracks/*' => \Illuminate\Support\Facades\Http::response([
                'estat_solicitud_id' => 3,
                'incoterm_id' => 1,
                'historial' => []
            ], 200),
            '*api/incoterms/*' => \Illuminate\Support\Facades\Http::response([
                'TrackingStep' => 'En Tránsito' // <--- Prueba a cambiar esto por "Recogida" o "Entrega Final"
            ], 200),
        ]);
        // ----------------------------------------------

        // 1. Datos que traemos nosotros
        $solicitud = solicitud::delClienteActual()
            ->with(['port_origen', 'port_desti', 'tipus_carrega', 'operacio'])
            ->findOrFail($id);

        // 1.1. Determinamos la referencia visual (Usamos OP- si existe operacion)
        $refFinal = 'SOL-' . str_pad($solicitud->id, 5, '0', STR_PAD_LEFT);
        if ($solicitud->operacio) {
            // Si hay operación, usamos SU código tal cual (OP-0005)
            $refFinal = $solicitud->operacio->codi_referencia;
        }

        // 1.2. Inicializamos variables para evitar errores si la API no responde
        $pasoActual = 'Pendent';
        $statusExterno = [];
        $urlBase = env('URL_API_TRACK');

        // 2. Llamada a API DAM
        // Try-catch por si su servidor está caído
        if ($urlBase) {
            try {
                // Estado operacion
                // El trim es para evitar que se duplique la barra si la url termina en / y la ruta en /api
                // Usamos la $id de la solicitud para la API externa
                $urlTracks = rtrim($urlBase, '/') . '/api/tracks/' . $id;
                $response = Http::timeout(3)->get($urlTracks); // Timeout de 3 seg para no bloquear

                // Si la API devuelve nada o error en la primera call no se va a ejecutar 
                // nada, más que nada para que la web no pete porque tendriamos el incoterm[] vacio 
                // y vue se volveria loco
                if ($response->successful()) {
                    $statusExterno = $response->json();
                    $incotermId = $statusExterno['incoterm_id'] ?? 0;
                    if ($incotermId > 0) {
                        $urlInco = rtrim($urlBase, '/') . '/api/incoterms/' . $incotermId;
                        $incotermRes = Http::timeout(3)->get($urlInco);
                        if ($incotermRes->successful()) {
                            $pasoActual = $incotermRes->json()['TrackingStep'] ?? 'Pendent';
                        }
                    }
                }
            } catch (\Exception $e) {
                // Si la API falla, no hacemos nada, la web seguirá funcionando
                $pasoActual = 'Seguiment offline';
            }
        }

        // 3. Devolvemos un JSON unificado
        return response()->json([
            'status' => 'success',
            'data' => [
                'ref' => $refFinal,
                'origen' => $solicitud->port_origen->nom ?? 'N/A',
                'desti' => $solicitud->port_desti->nom ?? 'N/A',
                'carrega' => $solicitud->tipus_carrega->nom ?? 'N/A',
                'pes_brut' => $solicitud->pes_brut ?? '—',
                'tracking' => [
                    'estado_id' => $statusExterno['estat_solicitud_id'] ?? $solicitud->estat_solicitud_id,
                    'paso_nombre' => $pasoActual,
                    'ultima_actualizacion' => now()
                ],
                'historial' => $statusExterno['historial'] ?? []
            ]
        ]);
    }
}