<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Models\ciutats;
use App\Models\incoterm;
use App\Models\operacions;
use App\Models\paissos;
use App\Models\peticions_registre;
use App\Models\ports;
use App\Models\usuaris;
use App\Models\solicitud;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        return match ((int) $user->rol_id) {
            1 => $this->getAdminData(),
            2 => $this->getUserData($user),
            3 => $this->getOperadorData($user),
            default => response()->json(['error' => 'Rol no reconocido'], 403),
        };
    }

    // ─────────────────────────────────────────────
    //  ADMIN
    // ─────────────────────────────────────────────

    private function getAdminData(): JsonResponse
    {
        $stats = [
            'totalUsuarios' => usuaris::count(),
            'peticionesPendientes' => peticions_registre::where('estat', '0')->count(),
            'totalOperadores' => usuaris::where('rol_id', 3)->count(),
        ];

        $monitorCarga = $this->getMonitorCarga();

        $ultimosUsuarios = usuaris::latest()
            ->take(5)
            ->get()
            ->map(fn($u) => [
                'nombre' => $u->nom,
                'rol' => match ((int) $u->rol_id) {
                    1 => 'Admin',
                    3 => 'Operador',
                    default => 'Cliente',
                },
                'fecha' => $u->created_at
                    ? Carbon::parse($u->created_at)->format('d/m/Y')
                    : 'N/A',
            ]);

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'monitorCarga' => $monitorCarga,
            'ultimosUsuarios' => $ultimosUsuarios,
        ]);
    }

    private function getMonitorCarga(): \Illuminate\Support\Collection
    {
        $sources = [
            [
                'model' => ports::class,
                'label' => 'Puerto',
                'display' => fn($i) => $i->nom,
            ],
            [
                'model' => Incoterm::class,
                'label' => 'Incoterm',
                'display' => fn($i) => $i->name,
            ],
            [
                'model' => paissos::class,
                'label' => 'País',
                'display' => fn($i) => $i->nom,
            ],
            [
                'model' => ciutats::class,
                'label' => 'Ciudad',
                'display' => fn($i) => $i->nom,
            ],
        ];

        return collect($sources)
            ->flatMap(function ($src) {
                return $src['model']::with('addedBy')
                    ->latest('updated_at')
                    ->take(3)
                    ->get()
                    ->map(fn($item) => [
                        'entidad' => $src['label'],
                        'registro' => ($src['display'])($item),
                        'fecha' => $item->updated_at ?? $item->created_at,
                        'accion' => (
                            $item->updated_at &&
                            $item->created_at &&
                            Carbon::parse($item->updated_at)->gt(Carbon::parse($item->created_at))
                        ) ? 'Actualitzat' : 'Nou',
                        // Incoterm no tiene addedBy, los demás sí
                        'encargada' => $item->addedBy?->nom ?? 'Desconegut',
                    ]);
            })
            ->sortByDesc('fecha')
            ->take(5)
            ->values()
            ->map(fn($item) => [
                ...$item,
                'fecha' => Carbon::parse($item['fecha'])->format('d/m/Y H:i'),
            ]);
    }

    // ─────────────────────────────────────────────
    //  OPERADOR
    // ─────────────────────────────────────────────

    private function getOperadorData($user): JsonResponse
    {
        $fechaLimite = Carbon::now()->subDays(3);

        // CORRECCIÓN: solicitud no tiene campo 'estat', el estado es
        // 'estat_solicitud_id'. Filtramos por los IDs correspondientes.
        // Asumiendo: 3 = cotizada, 4 = en_negociacion (ajusta según tus datos)
        $cotizacionesFrias = solicitud::with(['port_origen', 'port_desti', 'estat_solicitud'])
            ->whereIn('estat_solicitud_id', [3, 4])
            ->where('updated_at', '<', $fechaLimite)
            ->orderBy('updated_at', 'asc')
            ->get()
            ->map(fn($s) => [
                'id' => $s->id,
                'codigo' => 'REQ-00' . $s->id,
                'ruta' => ($s->port_origen->nom ?? 'N/A') . ' → ' . ($s->port_desti->nom ?? 'N/A'),
                'estado' => $s->estat_solicitud->estat ?? 'Desconegut',
                'dias_sin_cambios' => Carbon::parse($s->updated_at)->diffInDays(Carbon::now()),
                'ultima_actualizacion' => $s->updated_at
                    ? Carbon::parse($s->updated_at)->format('Y-m-d')
                    : 'N/A',
            ]);

        $actividadRecienteMia = solicitud::with('estat_solicitud')
            ->where('operador_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get()
            ->map(fn($s) => [
                'id' => $s->id,
                'codigo' => 'REQ-00' . $s->id,
                'estado_actual' => $s->estat_solicitud->estat ?? 'Desconegut',
                'tiempo_transcurrido' => Carbon::parse($s->updated_at)->diffForHumans(),
            ]);

        return response()->json([
            'success' => true,
            'cotizacionesFrias' => $cotizacionesFrias,
            'actividadRecienteMia' => $actividadRecienteMia,
        ]);
    }

    // ─────────────────────────────────────────────
    //  CLIENTE
    // ─────────────────────────────────────────────

    private function getUserData($user): JsonResponse
    {
        $clienteId = $user->clients->id ?? null;

        if (!$clienteId) {
            return response()->json(['error' => 'Cliente no encontrado'], 404);
        }

        $stats = [
            'solicitudes_enviadas' => solicitud::where('client_id', $clienteId)
                ->whereIn('estat_solicitud_id', [1, 2])
                ->count(),
            'pedidos_activos' => solicitud::where('client_id', $clienteId)
                ->whereIn('estat_solicitud_id', [3, 4])
                ->count(),
            // Contamos ofertes vinculadas a solicitudes del cliente
            'docs_por_revisar' => solicitud::where('client_id', $clienteId)
                ->whereIn('estat_solicitud_id', [3])
                ->count(),
        ];

        //operacio → oferta → solicitud → portOrigen/portDesti
        $pedidosEnTransito = operacions::with([
            'solicitud.port_origen',
            'solicitud.port_desti',
            'solicitud.estat_solicitud',
            'estat',
        ])
            ->where('client_id', $clienteId)
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get()
            ->map(fn($op) => [
                'id' => $op->id,
                'codigo' => $op->codi_referencia ?? 'OP-' . $op->id,
                'ruta' => ($op->solicitud->port_origen->nom ?? 'N/A')
                    . ' → '
                    . ($op->solicitud->port_desti->nom ?? 'N/A'),
                'fecha_inicio' => $op->data_inici
                    ? Carbon::parse($op->data_inici)->format('d/m/Y')
                    : 'Pendiente',
                'estado_humano' => $this->getEstadoHumanoCorto(
                    $op->solicitud->estat_solicitud->estat ?? 'desconocido'
                ),
                'estado_id' => $op->solicitud->estat_solicitud_id ?? 'desconocido',
            ]);

        $misSolicitudes = solicitud::with(['port_origen', 'port_desti', 'estat_solicitud'])
            ->where('client_id', $clienteId)
            ->orderBy('data_creacio', 'desc')
            ->take(4)
            ->get()
            ->map(fn($s) => [
                'id' => $s->id,
                'tipo_transporte' => $s->tipus_transport_id ?? 'Carga General',
                'ruta' => ($s->port_origen->nom ?? 'N/A') . ' → ' . ($s->port_desti->nom ?? 'N/A'),
                'estado' => $s->estat_solicitud_id ?? 'desconocido',
                'sub_estado' => $this->getEstadoHumanoCorto(
                    $s->estat_solicitud->estat ?? 'desconocido'
                ),
            ]);

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'pedidos_en_transito' => $pedidosEnTransito,
            'mis_solicitudes' => $misSolicitudes,
        ]);
    }

    // ─────────────────────────────────────────────
    //  BUSCADOR
    // ─────────────────────────────────────────────

    public function buscadorSolicitud(Request $request): JsonResponse
    {
        $request->validate(['termino' => 'required|string']);

        $idLimpio = preg_replace('/[^0-9]/', '', $request->input('termino'));

        if (empty($idLimpio)) {
            return response()->json([
                'success' => false,
                'message' => 'Por favor, incluye el número de la solicitud.',
            ], 400);
        }

        $resultado = solicitud::with(['portOrigen', 'portDesti', 'estatSolicitud'])
            ->find((int) $idLimpio);

        if (!$resultado) {
            return response()->json([
                'success' => false,
                'message' => 'No se ha encontrado ninguna operación con ese código.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'operacion' => [
                'id' => $resultado->id,
                'estado' => $resultado->estatSolicitud->estat ?? 'desconocido',
                'ruta' => ($resultado->portOrigen->nom ?? 'N/A') . ' → ' . ($resultado->portDesti->nom ?? 'N/A'),
                'fecha_actualizacion' => $resultado->updated_at
                    ? Carbon::parse($resultado->updated_at)->format('d/m/Y H:i')
                    : 'N/A',
            ],
        ]);
    }

    // ─────────────────────────────────────────────
    //  HELPERS
    // ─────────────────────────────────────────────

    private function getEstadoHumanoCorto(string $estado): string
    {
        return match (strtolower($estado)) {
            'nueva' => 'Nueva',
            'en_revision' => 'En Revisión',
            'cotizada' => 'Cotizada',
            'en_negociacion' => 'En Negociación',
            'rechazada' => 'Rechazada',
            default => 'Desconocido',
        };
    }
}