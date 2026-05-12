<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Models\solicitud;

class TrackingController extends Controller
{
    public function show($id)
    {
        // 1. Cargamos la solicitud con relaciones locales
        $solicitud = solicitud::delClienteActual()
            ->with(['operacio.documents.tipo_documento', 'port_origen', 'port_desti', 'tipus_carrega'])
            ->findOrFail($id);

        $operacio = $solicitud->operacio;

        // 1.1. Referencia inteligente
        $refBase = $operacio->codi_referencia ?? ('OP-' . str_pad($solicitud->id, 5, '0', STR_PAD_LEFT));

        // 1.2. Datos por defecto
        $data = [
            'ref' => $refBase,
            'origen' => $solicitud->port_origen->nom ?? '—',
            'desti' => $solicitud->port_desti->nom ?? '—',
            'carrega' => $solicitud->tipus_carrega->tipus ?? '—',
            'pes_brut' => $solicitud->pes_brut,
            'tracking' => [
                'estado_id' => $solicitud->estat_solicitud_id,
                'paso_nombre' => 'Pendent',
                'ultima_actualizacion' => now()
            ],
            'historial' => [],
            'documentos' => $operacio ? $operacio->documents : []
        ];

        // 2. Si hay operación, consultamos la API externa
        if ($operacio) {
            $urlBase = env('URL_API_TRACK');
            if ($urlBase) {
                try {
                    $url = rtrim($urlBase, '/') . '/api/operation/' . $operacio->id;
                    $response = Http::timeout(3)->get($url);
                    if ($response->successful()) {
                        $ext = $response->json();

                        $nuevoEstado = $ext['estat_actual'] ?? $data['tracking']['paso_nombre'];

                        // 3. DETECCIÓN DE CAMBIOS PARA NOTIFICACIONES
                        if ($operacio->ultim_estat !== $nuevoEstado) {
                            \App\Models\notification::create([
                                'user_id' => auth()->id(),
                                'solicitud_id' => $solicitud->id,
                                'titol' => 'Actualització de seguiment',
                                'missatge' => "El teu pedido {$operacio->codi_referencia} ha canviat a: {$nuevoEstado}",
                                'date_update' => now()
                            ]);

                            $operacio->update(['ultim_estat' => $nuevoEstado]);
                        }

                        $data['ref'] = $ext['operacio'] ?? $data['ref'];
                        $data['origen'] = $ext['port_origen'] ?? $data['origen'];
                        $data['desti'] = $ext['port_desti'] ?? $data['desti'];
                        $data['carrega'] = $ext['tipus_carrega'] ?? $data['carrega'];
                        $data['pes_brut'] = $ext['pes_brut'] ?? $data['pes_brut'];
                        $data['tracking']['estado_id'] = $ext['estado_id'] ?? $data['tracking']['estado_id'];
                        $data['tracking']['paso_nombre'] = $nuevoEstado;
                        $data['historial'] = $ext['historial'] ?? $data['historial'];
                    }
                } catch (\Exception $e) {
                    $data['tracking']['paso_nombre'] = 'Seguiment offline';
                }
            }
        }

        return response()->json(['status' => 'success', 'data' => $data]);
    }
}