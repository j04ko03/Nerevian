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
use App\Models\ofertes;
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
        $cotizacionesFrias = solicitud::with(['portOrigen', 'portDesti', 'estatSolicitud'])
            ->whereIn('estat_solicitud_id', [3, 4])
            ->where('updated_at', '<', $fechaLimite)
            ->orderBy('updated_at', 'asc')
            ->get()
            ->map(fn($s) => [
                'id' => $s->id,
                'codigo' => 'REQ-00' . $s->id,
                'ruta' => ($s->portOrigen->nom ?? 'N/A') . ' → ' . ($s->portDesti->nom ?? 'N/A'),
                // CORRECCIÓN: leemos el estado desde la relación
                'estado' => $s->estatSolicitud->estat ?? 'Desconegut',
                'dias_sin_cambios' => Carbon::parse($s->updated_at)->diffInDays(Carbon::now()),
                'ultima_actualizacion' => $s->updated_at
                    ? Carbon::parse($s->updated_at)->format('Y-m-d')
                    : 'N/A',
            ]);

        // CORRECCIÓN: solicitud sí tiene operador_id, esto es correcto
        $actividadRecienteMia = solicitud::with('estatSolicitud')
            ->where('operador_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get()
            ->map(fn($s) => [
                'id' => $s->id,
                'codigo' => 'REQ-00' . $s->id,
                // CORRECCIÓN: estado desde relación, no campo directo
                'estado_actual' => $s->estatSolicitud->estat ?? 'Desconegut',
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
        // CORRECCIÓN: clients.usuari_id apunta a usuaris.id,
        // por lo que el clienteId es el id del usuario autenticado
        $clienteId = $user->client->id ?? null;

        if (!$clienteId) {
            return response()->json(['error' => 'Cliente no encontrado'], 404);
        }

        $stats = [
            // CORRECCIÓN: campo correcto es 'client_id', no 'cliente_id'
            // y filtramos por estat_solicitud_id, no por 'estat'
            // Asumiendo: 1 = nueva, 2 = en_revision (ajusta según tus datos)
            'solicitudes_enviadas' => solicitud::where('client_id', $clienteId)
                ->whereIn('estat_solicitud_id', [1, 2])
                ->count(),
            'pedidos_activos' => solicitud::where('client_id', $clienteId)
                ->whereIn('estat_solicitud_id', [3, 4])
                ->count(),
            // CORRECCIÓN: ofertes no tiene campo 'enviada' ni 'cliente_id'
            // Contamos ofertes vinculadas a solicitudes del cliente
            'docs_por_revisar' => ofertes::whereHas(
                'solicitud',
                fn($q) =>
                $q->where('client_id', $clienteId)
            )
                ->whereIn('estat_oferta_id', [1]) // ajusta el ID de estado "pendiente"
                ->count(),
        ];

        // CORRECCIÓN: operacions → oferta → solicitud para llegar a los puertos
        // La cadena correcta es: operacio → oferta → solicitud → portOrigen/portDesti
        $pedidosEnTransito = operacions::with([
            'oferta.solicitud.portOrigen',
            'oferta.solicitud.portDesti',
            'oferta.solicitud.estatSolicitud',
            'estat',
        ])
            ->where('client_id', $clienteId)
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get()
            ->map(fn($op) => [
                'id' => $op->id,
                'codigo' => $op->codi_referencia ?? 'OP-' . $op->id,
                // CORRECCIÓN: ruta viene de solicitud, no de oferta directamente
                'ruta' => ($op->oferta->solicitud->portOrigen->nom ?? 'N/A')
                    . ' → '
                    . ($op->oferta->solicitud->portDesti->nom ?? 'N/A'),
                'fecha_inicio' => $op->data_inici
                    ? Carbon::parse($op->data_inici)->format('d/m/Y')
                    : 'Pendiente',
                'estado_humano' => $this->getEstadoHumanoCorto(
                    $op->oferta->solicitud->estatSolicitud->estat ?? 'desconocido'
                ),
                'estado_id' => $op->oferta->solicitud->estat_solicitud_id ?? 'desconocido',
            ]);

        // CORRECCIÓN: client_id en lugar de cliente_id
        $misSolicitudes = solicitud::with(['portOrigen', 'portDesti', 'estatSolicitud'])
            ->where('client_id', $clienteId)
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get()
            ->map(fn($s) => [
                'id' => $s->id,
                'tipo_transporte' => $s->tipus_transport_id ?? 'Carga General',
                'ruta' => ($s->portOrigen->nom ?? 'N/A') . ' → ' . ($s->portDesti->nom ?? 'N/A'),
                // CORRECCIÓN: estado desde relación
                'estado' => $s->estat_solicitud_id ?? 'desconocido',
                'sub_estado' => $this->getEstadoHumanoCorto(
                    $s->estatSolicitud->estat ?? 'desconocido'
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

        // CORRECCIÓN: añadimos estatSolicitud para leer el estado correctamente
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
                // CORRECCIÓN: estado desde relación
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