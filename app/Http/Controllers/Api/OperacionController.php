<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\operacions;
use Illuminate\Http\JsonResponse;

class OperacionController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $operacions = operacions::with([
                'estat',
                'operador',
                'solicitud.port_origen.ciutat',
                'solicitud.port_desti.ciutat',
                'solicitud.tipus_transport',
            ])
            ->delClienteActual() // queryscope de operacions.php
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $operacions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        $operacio = operacions::with(['estat', 'operador', 'oferta', 'documents'])
            ->delClienteActual() // queryscope de operacions.php
            ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $operacio
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
