<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\documents;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use App\Models\solicitud;
use App\Models\operacions;

class DocumentController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = documents::with(['tipo_documento', 'usuari', 'solicitud', 'operacio']);

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

        return response()->json([
            'status' => 'success',
            'data' => $query->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'fitxer' => 'required|file|max:10000', // 10mb
            'tipus_document' => 'required|exists:tipo_documento,id',
            'operacio_id' => 'nullable|exists:operacions,id',
            'solicitud_id' => 'nullable|exists:solicitud,id'
        ]);

        $file = $request->file('fitxer');

        // Guardar el archivo físicamente
        // 'private' significa que se guarda en la carpeta storage/app/private
        // Para que funcione, he ejecutado php artisan storage:link y configurado el config/filesystems.php/disks (este archivo hay que cambiarlo cuando pasemos al servidor)
        $path = $file->store('documentos' /*carpeta que se va a crear en storage/app/private */ , 'private');

        // Registro en base de datos
        $document = documents::create([
            'nom_original' => $file->getClientOriginalName(),
            'nom_fitxer' => $file->hashName(), // hashName() devuelve el nombre del archivo con un hash para que no haya colisiones
            'ruta_fitxer' => $path,
            'mida' => $file->getSize(), // getSize() devuelve el tamaño del archivo en bytes
            'tipus_document' => $request->tipus_document,
            'operacio_id' => $request->operacio_id,
            //TODO: Hay que hacer una migración para que solicitud_id sea nullable y poder relacionar documentos con solicitudes.
            //'solicitud_id' => $request->solicitud_id,
            'pujat_per' => auth()->id() // auth()->id() devuelve el id del usuario autenticado
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Documento subido correctamente en private',
            'data' => $document
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        $document = documents::with('tipus_document')->findOrFail($id);

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
        $document->update(['tipus_document' => $request->tipo_documento]);

        return response()->json([
            'status' => 'success',
            'message' => 'Documento actualizado',
            'data' => $document
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */

    // I know that we said to dont delete docuemnts but yes to update them, 
    // but I thought that if we want to keep them once they're changed we 
    // should delete them from the storage and from the database. Otherwise 
    // we'll have archivos huerfano', sabes?
    public function destroy(string $id)
    {
        $document = documents::findOrFail($id);
        $user = auth()->user();

        if ($document->solicitud_id) {
            $propiedad = solicitud::delClienteActual()->where('id', $document->solicitud_id)->exists();
        } elseif ($document->operacio_id) {
            $propiedad = operacions::delClienteActual()->where('id', $document->operacio_id)->exists();
        } else {
            $propiedad = ($document->pujat_per == $user->id);
        }
        if (!$propiedad) {
            return response()->json([
                'status' => 'error',
                'message' => 'No tienes permiso para borrar este archivo'
            ], 403);
        }
        // Si tiene permiso puede acceder a sus documentos:
        if (Storage::disk('private')->exists($document->ruta_fitxer)) {
            Storage::disk('private')->delete($document->ruta_fitxer);
        }
        $document->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Documento eliminado correctamente'
        ]);
    }

    public function download($id)
    {
        $document = documents::findOrFail($id);
        $user = auth()->user();

        // Validamos que el documento pertenezca al cliente
        // Si el documento es de una solicitud, miramos si esa solicitud es del cliente actual
        if ($document->solicitud_id) {
            $propiedad = solicitud::delClienteActual()->where('id', $document->solicitud_id)->exists();
        } elseif ($document->operacio_id) {
            $propiedad = operacions::delClienteActual()->where('id', $document->operacio_id)->exists();
        } else {
            $propiedad = ($document->pujat_per == $user->id);
        }

        if (!$propiedad) {
            return response()->json([
                'status' => 'error',
                'message' => 'No tienes permiso para este archivo'
            ], 403);
        }

        // Verificamos existencia física en storage/private
        if (!Storage::disk('private')->exists($document->ruta_fitxer)) {
            return response()->json([
                'status' => 'error',
                'message' => 'El archivo físico no existe'
            ], 404);
        }

        // Retornamos la descarga
        return Storage::disk('private')->download
            /*Está amarillo por cosas de Laravel, FilesystemAdapter tiene el método y 
            es lo que funciona por atrás.*/
        ($document->ruta_fitxer, $document->nom_original);
    }
}
