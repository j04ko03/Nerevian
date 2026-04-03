<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\ciutats;         
use App\Models\paissos;        
use App\Models\ports;
use App\Models\tipus_incoterms;  
use App\Models\usuaris;
use App\Models\peticions_registre;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user(); 
        
        // Si es Admin (rol_id = 1)
        if ($user->rol_id === 1) {
            return $this->getAdminData();
        }
        
        // Si es Cliente (rol_id = 2)
        if ($user->rol_id === 2) {
            return $this->getUserData($user);
        }

        // Si es Operador (rol_id = 3)
        if ($user->rol_id === 3) {
            return $this->getOperadorData($user);
        }

    }

    public function getAdminData()
    {
        // ── 1. STATS (KPIs) ──────────────────────────────────
        
        // Total usuarios
        $totalUsuarios = usuaris::all()->count();

        // Peticiones pendientes
        $peticionesPendientes = peticions_registre::where('estat', '0')->count();
        
        // Total operadores
        $totalOperadores = usuaris::where('rol_id', 3)->count();

        $stats = [
            'totalUsuarios' => $totalUsuarios,
            'peticionesPendientes' => $peticionesPendientes,
            'totalOperadores' => $totalOperadores,
        ];
        // ── 2. LAST MODIFICATIONS  ──────────────────────────────────        
        // 2.1 Últimos 3 puertos modificados
        $ultimosPuertos = ports::latest('updated_at')->take(3)->get()->map(function ($item) {
            return [
                'entidad'  => 'Puerto',
                'registro' => $item->nom,
                'fecha'    => $item->updated_at
            ];
        });

        // 2.2 Últimos 3 incoterms modificados
        $ultimosIncoterms = tipus_incoterms::latest('updated_at')->take(3)->get()->map(function ($item) {
            return [
                'entidad'  => 'Incoterm',
                'registro' => $item->codi . ' - ' . $item->nom,
                'fecha'    => $item->updated_at
            ];
        });

        // 2.3 Últimos 3 países modificados
        $ultimosPaissos = paissos::latest('updated_at')->take(3)->get()->map(function ($item) {
            return [
                'entidad'  => 'País',
                'registro' => $item->nom,
                'fecha'    => $item->updated_at
            ];
        });

        // 2.4 Últimos 3 ciudades modificados
        $ultimosCiutats = ciutats::latest('updated_at')->take(3)->get()->map(function ($item) {
            return [
                'entidad'  => 'Ciudad',
                'registro' => $item->nom,
                'fecha'    => $item->updated_at
            ];
        });

        // Unificar las 4 tablas, ordenar de más nuevo a más viejo y formatear
        $monitorCarga = collect()
            ->concat($ultimosPuertos)
            ->concat($ultimosIncoterms)
            ->concat($ultimosPaissos)
            ->concat($ultimosCiutats)
            ->sortByDesc('fecha')
            ->take(5) // Nos quedamos solo con los 5 más recientes a nivel global
            ->values()
            ->map(function ($item) {
                // Ensure $item is an array
                $arr = (array) $item;
                $fechaStr = 'Sin fecha';
                if (!empty($arr['fecha'])) {
                    $fechaStr = $arr['fecha'] instanceof \Carbon\Carbon || $arr['fecha'] instanceof \DateTime 
                        ? $arr['fecha']->format('d/m/Y H:i') 
                        : (string)$arr['fecha'];
                }
                
                return [
                    'entidad'  => $arr['entidad'],
                    'registro' => $arr['registro'],
                    'fecha'    => $fechaStr,
                    'accion'   => 'Actualizado' 
                ];
            });

        // ── 3. LAST USER REGISTRATIONS ──────────────────────────────────
        $ultimosUsuarios = usuaris::latest('created_at')
            ->take(5)
            ->get()
            ->map(function ($usuario) {
                return [
                    'nombre' => $usuario->nom,
                    'rol'    => $usuario->rol_id == 1 ? 'Admin' : 'Operador',
                    'fecha'  => isset($usuario->created_at) && ($usuario->created_at instanceof \Carbon\Carbon || $usuario->created_at instanceof \DateTime) 
                        ? $usuario->created_at->format('d/m/Y') 
                        : (isset($usuario->created_at) ? (string)$usuario->created_at : 'Antiguo'),
                ];
            });
        return response()->json([
            'success' => true,
            'stats' => $stats,
            'monitorCarga' => $monitorCarga,
            'ultimosUsuarios' => $ultimosUsuarios,
        ], 200);
    }

    private function getOperadorData($user)
    {
        // Datos distintos para el supervisor
        return response()->json(['data' => 'datos de supervisor']);
    }

    private function getUserData($user)
    {
        // Datos personales del usuario
        return response()->json(['data' => 'datos de usuario']);
    }
}