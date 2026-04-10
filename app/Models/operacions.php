<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class operacions extends Model
{

    // Desde aqui gestionaremos los pedidos/encargos y el tracking (el pedido en sí y los estados del tracking me refiero)
    protected $table = 'operacions';
    protected $fillable = [
        'oferta_id',
        'codi_referencia',
        'estat_id',
        'operador_id',
        'client_id',
        'data_inici',
        'data_finalitzacio',
        'observacions',
        'created_at',
        'updated_at'
    ];

    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class, 'solicitud_id');
    }

    // Para que podamos ver a quién pertenece, el estado y cual oferta generó el encargo/pedido.
    public function oferta(): BelongsTo
    {
        return $this->belongsTo(ofertes::class, 'oferta_id');
    }
    public function estat(): BelongsTo
    {
        return $this->belongsTo(estats_operacions::class, 'estat_id');
    }
    public function operador(): BelongsTo
    {
        return $this->belongsTo(usuaris::class, 'operador_id');
    }
    public function client(): BelongsTo
    {
        return $this->belongsTo(clients::class, 'client_id');
    }

    // Para traer los documentos de un pedido
    public function documents(): HasMany
    {
        return $this->hasMany(documents::class, 'operacio_id');
    }
}
