<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class solicitud extends Model
{
    protected $table = 'solicitud';
    protected $primaryKey = 'id';

    const CREATED_AT = 'data_creacio';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
        'pes_brut',
        'volum',
        'comentaris',
        'data_creacio',
        'updated_at',

        'tipus_transport_id', // FK
        'tipus_fluxe_id', // FK
        'tipus_carrega_id', // FK
        'incoterm_id', // FK
        'client_id', // FK
        'transportista_id', // FK
        'tipus_validacio_id', // FK
        'port_origen_id', // FK
        'port_desti_id', // FK
        'estat_solicitud_id', // FK
        'operador_id', // FK
        'tipus_contenidor_id', // FK
    ];

    public function tipus_transport()
    {
        return $this->belongsTo(tipus_transports::class, 'tipus_transport_id');
    }

    public function tipus_fluxe()
    {
        return $this->belongsTo(tipus_fluxes::class, 'tipus_fluxe_id');
    }

    public function tipus_carrega()
    {
        return $this->belongsTo(tipus_carrega::class, 'tipus_carrega_id');
    }

    public function tipus_incoterm()
    {
        return $this->belongsTo(tipus_incoterms::class, 'incoterm_id');
    }

    public function client()
    {
        return $this->belongsTo(clients::class, 'client_id');
    }

    public function transportista()
    {
        return $this->belongsTo(transportistes::class, 'transportista_id');
    }

    public function tipus_validacio()
    {
        return $this->belongsTo(tipus_validacions::class, 'tipus_validacio_id');
    }

    public function port_origen()
    {
        return $this->belongsTo(ports::class, 'port_origen_id');
    }

    public function port_desti()
    {
        return $this->belongsTo(ports::class, 'port_desti_id');
    }

    public function estat_solicitud()
    {
        return $this->belongsTo(estats_solicituds::class, 'estat_solicitud_id');
    }

    public function operador()
    {
        return $this->belongsTo(usuaris::class, 'operador_id');
    }

    public function tipus_contenidor()
    {
        return $this->belongsTo(tipus_contenidors::class, 'tipus_contenidor_id');
    }

    // QUERY SCOPES
    // Los queryscopes son funciones que encapsulan (guardan) peticiones de búsqueda básica o común, 
    // como lo quieras llamar, para no tener que escribirlas cada vez (me parece la polla).
    // Se escriben en el modelo de la tabla que quieres consultar y lo puedes llamar desde cualquier controller.
    // Una característica que me gusta bastante es que puedes concatenarlos.

    // Por ejemplo, si siempre quieres buscar por cliente, puedes crear un queryscope para ello.


    // scope para verificar si hay un cliente logueado y así se le devuelven sus solicitudes.
    public function scopeDelClienteActual($query)
    {
        $clientId = auth()->user()->id ?? null;
        return $query->where('client_id', $clientId);
    }

    // scope para filtrar el estado de las solicitudes. Si buscamos pendientes (1) pues sería blablabla ->delEstado(1)
    public function scopeDelEstado($query, $estadoId)
    {
        return $query->where('estat_solicitud_id', $estadoId);
    }

}
