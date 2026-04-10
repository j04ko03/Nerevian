<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class documents extends Model
{
    protected $table = 'documents';

    // Creo las const para que Eloquent reconozca las fechas, porque por default busca created_at y updated_at
    const CREATED_AT = 'data_pujada';
    const UPDATED_AT = null; // Lo defino como null para que Eloquent no me de error :) (no tenemos este campo en la base de datos)

    protected $fillable = [
        'nom_fitxer',
        'nom_original',
        'tipus_document',
        'ruta_fitxer',
        'mida',
        'oferta_id',
        'operacio_id',
        'pujat_per',
    ];
    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class, 'solicitud_id');
    }


    // Funciones para saber de qué es cada documento.
    public function tipo_documento()
    {
        return $this->belongsTo(tipo_documento::class, 'tipus_document');
    }
    public function oferta()
    {
        return $this->belongsTo(ofertes::class, 'oferta_id');
    }
    public function operacio()
    {
        return $this->belongsTo(operacions::class, 'operacio_id');
    }
    public function usuari()
    {
        return $this->belongsTo(usuaris::class, 'pujat_per');
    }

}
