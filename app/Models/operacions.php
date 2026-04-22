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

    // QUERY SCOPES
    // Los queryscopes son funciones que encapsulan (guardan) peticiones de búsqueda básica o común, 
    // como lo quieras llamar, para no tener que escribirlas cada vez (me parece la polla).
    // Se escriben en el modelo de la tabla que quieres consultar y lo puedes llamar desde cualquier controller.
    // Una característica que me gusta bastante es que puedes concatenarlos.

    // scope para verificar si hay un cliente logueado y así se le devuelven sus solicitudes.
    public function scopeDelClienteActual($query)
    {
        $clientId = auth()->user()->clients->id ?? null;
        return $query->where('client_id', $clientId);
    }

    // scope para filtrar el estado de las solicitudes. Si buscamos pendientes (1) pues sería blablabla ->EnEstado(1)
    public function scopeEnEstado($query, $estadoId)
    {
        return $query->where('estat_id', $estadoId);
    }
}
