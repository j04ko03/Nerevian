<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ofertes extends Model
{
    protected $table    = 'ofertes';
    public $timestamps  = false;
    protected $casts = [
        'estat_oferta_id' => 'integer',
        'solicitud_id'    => 'integer',
        'clients_id'      => 'integer',
        'pressupost'      => 'float',
        'es_contraoferta' => 'boolean',
    ];

    protected $fillable = [
        'solicitud_id',
        'clients_id',
        'pressupost',
        'comentaris',
        'estat_oferta_id',
        'deny_reason',
        'es_contraoferta',
        'data_creacio',
        'data_validessa_inicial',
        'data_validessa_final',
        'moneda',
    ];

    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(solicitud::class, 'solicitud_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(clients::class, 'clients_id');
    }

    public function estat(): BelongsTo
    {
        return $this->belongsTo(estats_ofertes::class, 'estat_oferta_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(documents::class, 'oferta_id');
    }
}