<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\documents;
use Illuminate\Http\JsonResponse;

class DocumentController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = documents::with(['tipo_documento', 'usuari']);

        // cancatenacioooon de queryscopes jeje
        // si viene operacio_id, filtra por operacio_id
        // si viene solicitud_id, filtra por solicitud_id
        // si viene operacio_id y solicitud_id, filtra por ambos
        if ($request->has('operacio_id')) {
            $query->deOperacion($request->operacio_id); // queryscope de documents.php
        }
        if ($request->has('solicitud_id')) {
            $query->deSolicitud($request->solicitud_id); // queryscope de documents.php
        }

        return response()->json(['status' => 'success', 'data' => $query->get()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'fitxer' => 'required|file|max:10000',
            'tipus_document' => 'required|exists:tipo_documento,id',
            'operacio_id' => 'nullable|exists:operacions,id',
            'solicitud_id' => 'nullable|exists:solicitud,id'
        ]);

        $file = $request->file('fitxer');

        // Guardar el archivo físicamente
        $path = $file->store('documents', 'local'); //meter private luego

        // Registro en base de datos
        $document = documents::create([
            'nom_original' => $file->getClientOriginalName(),
            'nom_fitxer' => $file->hashName(),
            'ruta_fitxer' => $path,
            'mida' => $file->getSize(),
            'tipus_document' => $request->tipus_document,
            'operacio_id' => $request->operacio_id,
            'solicitud_id' => $request->solicitud_id,
            'pujat_per' => auth()->id()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Documento subido correctamente',
            'data' => $document
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        $document = documents::with('tipo_documento')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $document
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'tipus_document' => 'required|exists:tipo_documento,id',
        ]);

        $document = documents::findOrFail($id);
        $document->update(['tipus_document' => $request->tipus_document]);

        return response()->json([
            'status' => 'success',
            'message' => 'Documento actualizado',
            'data' => $document
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    // 
    public function download($id)
    {
        $document = documents::findOrFail($id);

        if (!Storage::disk('public')->exists($document->ruta_fitxer)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Archivo no encontrado en el servidor'
            ], 404);
        }

        return Storage::disk('private')->download($document->ruta_fitxer, $document->nom_original);
    }
}
