<?php

namespace App\Services;

use App\Models\documents;
use App\Models\operacions;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentService
{
    /**
     * Genera el Bill of Lading (BL) para una operación y lo guarda en el sistema.
     * 
     * @param int $operacioId
     * @return documents
     */
    public function generarBL($operacioId)
    {
        // 1. Obtener la operación con todos los datos necesarios
        $operacion = operacions::with([
            'solicitud.client.usuari',
            'solicitud.port_origen',
            'solicitud.port_desti',
            'solicitud.tipus_carrega',
            'solicitud.tipus_contenidor',
            'solicitud.incoterm'
        ])->findOrFail($operacioId);

        $solicitud = $operacion->solicitud;

        // 2. Generar el PDF usando la vista blade
        $pdf = Pdf::loadView('pdfs.bl', [
            'operacion' => $operacion,
            'solicitud' => $solicitud
        ]);

        // 3. Definir nombres y rutas
        $fileName = 'BL-' . $operacion->codi_referencia . '-' . Str::random(5) . '.pdf';
        $path = 'documentos/' . $fileName;

        // 4. Guardar físicamente en el disco 'private'
        Storage::disk('private')->put($path, $pdf->output());

        // 5. Crear el registro en la base de datos
        // El ID 2 corresponde a 'Bill of Lading' según el insert previo del usuario
        return documents::create([
            'nom_original' => 'Bill of Lading - ' . $operacion->codi_referencia . '.pdf',
            'nom_fitxer' => $fileName,
            'ruta_fitxer' => $path,
            'mida' => Storage::disk('private')->size($path),
            'tipus_document' => 2, 
            'operacio_id' => $operacion->id,
            'solicitud_id' => $solicitud->id,
            'pujat_per' => auth()->id() ?? $operacion->operador_id,
        ]);
    }
}
